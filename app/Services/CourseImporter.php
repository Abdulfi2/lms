<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentRubric;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

/**
 * Mengimpor satu paket course (course, section, lesson, kuis, tugas, kategori, tag) dari JSON
 * buatan AI agent. Format dan batasan field mengikuti docs/ai-agent/*.md dan aturan validasi
 * form instruktur/admin, supaya hasil impor sama dengan hasil input manual.
 *
 * Konten dari AI selalu masuk sebagai draft: status course dipaksa 'draft', kuis dan tugas
 * dipaksa belum terbit (menerbitkan tugas memicu notifikasi ke peserta).
 */
class CourseImporter
{
    private const QUIZ_TYPES = 'practice,pretest,posttest,final';

    public function decode(string $json): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw new InvalidArgumentException('Berkas bukan JSON yang valid: ' . json_last_error_msg());
        }

        return $this->normalize($data);
    }

    /**
     * @return array{errors: string[], warnings: string[], summary: array, notes: string[]}
     */
    public function validate(array $data, bool $canCreateCategories): array
    {
        $errors = [];
        $warnings = [];

        $validator = Validator::make($data, $this->rules(), [], []);
        foreach ($validator->errors()->messages() as $key => $messages) {
            foreach ($messages as $message) {
                // Pesan bawaan menyisipkan nama field mentah ("sections.0.lessons.0.type"); label di
                // depan sudah menunjukkan lokasinya, jadi nama mentah diganti kata umum "value".
                $message = str_replace(str_replace('_', ' ', $key), 'value', $message);
                $errors[] = $this->label($key) . ': ' . $message;
            }
        }

        // Pemeriksaan silang hanya bermakna kalau struktur dasarnya sudah benar.
        if (empty($errors)) {
            [$crossErrors, $warnings] = $this->crossCheck($data, $canCreateCategories);
            $errors = $crossErrors;
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'summary' => $this->summary($data),
            'notes' => array_values(array_filter((array) ($data['catatan'] ?? []), 'is_string')),
        ];
    }

    public function summary(array $data): array
    {
        $sections = $data['sections'] ?? [];
        $lessons = collect($sections)->sum(fn ($s) => count($s['lessons'] ?? []));
        $questions = collect($data['quizzes'] ?? [])->sum(fn ($q) => count($q['questions'] ?? []));
        $resources = collect($sections)->sum(
            fn ($s) => collect($s['lessons'] ?? [])->sum(fn ($l) => count($l['resources'] ?? []))
        );

        return [
            'title' => $data['course']['title'] ?? '-',
            'sections' => count($sections),
            'lessons' => $lessons,
            'quizzes' => count($data['quizzes'] ?? []),
            'questions' => $questions,
            'assignments' => count($data['assignments'] ?? []),
            'resources' => $resources,
            'categories' => count($data['course']['categories'] ?? []),
            'tags' => count($data['course']['tags'] ?? []),
        ];
    }

    /**
     * Buat semua entitas dalam satu transaksi. Data harus sudah lolos validate().
     *
     * @return array{course: Course, counts: array, pending_uploads: string[], skipped_categories: string[]}
     */
    public function import(array $data, User $instructor, bool $canCreateCategories): array
    {
        return DB::transaction(function () use ($data, $instructor, $canCreateCategories) {
            $courseData = $data['course'];
            $skippedCategories = [];

            $course = Course::create([
                'title' => $courseData['title'],
                'slug' => $courseData['slug'] ?? null,
                'short_description' => $courseData['short_description'] ?? null,
                'description' => $courseData['description'],
                'requirements' => $courseData['requirements'] ?? [],
                'learning_objectives' => $courseData['learning_objectives'] ?? [],
                'target_audience' => $courseData['target_audience'] ?? [],
                'prerequisites' => $courseData['prerequisites'] ?? [],
                'video_promo' => $courseData['video_promo'] ?? null,
                'price' => $courseData['price'],
                'sale_price' => $courseData['sale_price'] ?? null,
                'sale_starts_at' => $courseData['sale_starts_at'] ?? null,
                'sale_ends_at' => $courseData['sale_ends_at'] ?? null,
                'level' => $courseData['level'],
                'language' => $courseData['language'],
                'duration_total' => $courseData['duration_total'] ?? 0,
                'status' => 'draft',
                'is_featured' => (bool) ($courseData['is_featured'] ?? false),
                'has_certificate' => (bool) ($courseData['has_certificate'] ?? false),
                'max_students' => $courseData['max_students'] ?? null,
                'enrollment_end_date' => $courseData['enrollment_end_date'] ?? null,
                'start_date' => $courseData['start_date'] ?? null,
                'end_date' => $courseData['end_date'] ?? null,
                'access_days' => $courseData['access_days'] ?? null,
                'meta_keywords' => $courseData['meta_keywords'] ?? null,
                'meta_description' => $courseData['meta_description'] ?? null,
                'instructor_id' => $instructor->id,
            ]);

            $categoryIds = $this->resolveCategories($data, $canCreateCategories, $skippedCategories);
            if ($categoryIds) {
                $course->categories()->sync($categoryIds);
            }

            $tagIds = $this->resolveTags($courseData['tags'] ?? []);
            if ($tagIds) {
                $course->tags()->sync($tagIds);
                Tag::whereIn('id', $tagIds)->increment('usage_count');
            }

            [$lessonsByTitle, $pendingUploads] = $this->createSections($course, $data['sections'] ?? []);
            $this->createQuizzes($course, $data['quizzes'] ?? [], $lessonsByTitle);
            $this->createAssignments($course, $data['assignments'] ?? [], $lessonsByTitle);

            // Section dibuat sebelum lesson-nya, jadi hitungan total_lessons/total_duration per section
            // (dan otomatis milik course) harus dihitung ulang di akhir.
            $course->sections()->get()->each->updateCounts();

            return [
                'course' => $course,
                'counts' => $this->summary($data),
                'pending_uploads' => $pendingUploads,
                'skipped_categories' => $skippedCategories,
            ];
        });
    }

    private function createSections(Course $course, array $sections): array
    {
        $lessonsByTitle = [];
        $pendingUploads = [];

        foreach ($sections as $sIndex => $sectionData) {
            $section = $course->sections()->create([
                'title' => $sectionData['title'],
                'description' => $sectionData['description'] ?? null,
                'order' => $sIndex + 1,
                'is_published' => (bool) ($sectionData['is_published'] ?? true),
            ]);

            foreach (($sectionData['lessons'] ?? []) as $lIndex => $lessonData) {
                $videoUrl = $lessonData['video_url'] ?? null;

                $lesson = $section->lessons()->create([
                    'title' => $lessonData['title'],
                    'type' => $lessonData['type'],
                    'content' => $lessonData['content'] ?? null,
                    'duration' => $lessonData['duration'] ?? 0,
                    'video_url' => $videoUrl ? Lesson::normalizeVideoUrl($videoUrl) : null,
                    'is_free_preview' => (bool) ($lessonData['is_free_preview'] ?? false),
                    'points' => $lessonData['points'] ?? 0,
                    'status' => $lessonData['status'] ?? 'draft',
                    'order' => $lIndex + 1,
                ]);

                $lessonsByTitle[$this->key($lesson->title)] = $lesson;

                foreach (($lessonData['resources'] ?? []) as $resource) {
                    $pendingUploads[] = '[' . $section->title . ' > ' . $lesson->title . '] ' . $resource['title'];
                }
            }
        }

        return [$lessonsByTitle, $pendingUploads];
    }

    private function createQuizzes(Course $course, array $quizzes, array $lessonsByTitle): void
    {
        foreach ($quizzes as $quizData) {
            $questions = $quizData['questions'] ?? [];
            $lesson = $this->lessonFor($quizData['lesson_title'] ?? null, $lessonsByTitle);

            $quiz = $course->quizzes()->create([
                'lesson_id' => $lesson?->id,
                'title' => $quizData['title'],
                'description' => $quizData['description'] ?? null,
                'quiz_type' => $quizData['quiz_type'] ?? 'practice',
                'time_limit' => $quizData['time_limit'] ?? 0,
                'attempts_allowed' => $quizData['attempts_allowed'] ?? 1,
                'passing_score' => $quizData['passing_score'] ?? 70,
                'randomize_questions' => (bool) ($quizData['randomize_questions'] ?? false),
                'is_published' => false,
                'total_questions' => count($questions),
            ]);

            foreach ($questions as $qIndex => $q) {
                $question = $quiz->questions()->create([
                    'question' => $q['question'],
                    'type' => $q['type'],
                    'points' => $q['points'],
                    'explanation' => $q['explanation'] ?? null,
                    'order' => $qIndex + 1,
                ]);

                if ($q['type'] === 'multiple_choice') {
                    foreach ($q['options'] as $oIndex => $option) {
                        $question->options()->create([
                            'option_text' => $option['text'],
                            'is_correct' => (bool) ($option['is_correct'] ?? false),
                            'points' => $option['points'] ?? 0,
                            'order' => $oIndex + 1,
                        ]);
                    }
                } elseif ($q['type'] === 'true_false') {
                    $question->options()->createMany([
                        ['option_text' => 'Benar', 'is_correct' => $q['correct_answer'] === 'true', 'order' => 1],
                        ['option_text' => 'Salah', 'is_correct' => $q['correct_answer'] === 'false', 'order' => 2],
                    ]);
                }
            }
        }
    }

    private function createAssignments(Course $course, array $assignments, array $lessonsByTitle): void
    {
        foreach ($assignments as $item) {
            $lesson = $this->lessonFor($item['lesson_title'] ?? null, $lessonsByTitle);

            $assignment = Assignment::create([
                'course_id' => $course->id,
                'lesson_id' => $lesson?->id,
                'title' => $item['title'],
                'description' => $item['description'],
                'instructions' => $item['instructions'] ?? null,
                'max_score' => $item['max_score'],
                'passing_score' => $item['passing_score'],
                'due_date' => $item['due_date'] ?? null,
                'is_published' => false,
                'allow_late_submission' => (bool) ($item['allow_late_submission'] ?? false),
                'late_penalty' => $item['late_penalty'] ?? null,
            ]);

            foreach (($item['rubric'] ?? []) as $index => $row) {
                AssignmentRubric::create([
                    'assignment_id' => $assignment->id,
                    'criteria' => $row['criteria'],
                    'description' => $row['description'] ?? null,
                    'max_points' => $row['max_points'],
                    'order' => $index,
                ]);
            }
        }
    }

    /**
     * Kategori yang sudah ada dipakai apa adanya. Kategori baru hanya dibuat bila dicantumkan di
     * new_categories dan pelaksananya boleh mengelola kategori (admin / perintah artisan).
     */
    private function resolveCategories(array $data, bool $canCreate, array &$skipped): array
    {
        $definitions = collect($data['new_categories'] ?? [])->keyBy(fn ($c) => $this->key($c['name']));
        $ids = [];

        foreach (($data['course']['categories'] ?? []) as $name) {
            $category = Category::whereRaw('LOWER(name) = ?', [$this->key($name)])->first();

            if (!$category && $canCreate && $definitions->has($this->key($name))) {
                $def = $definitions[$this->key($name)];
                $parent = !empty($def['parent'])
                    ? Category::whereRaw('LOWER(name) = ?', [$this->key($def['parent'])])->first()
                    : null;

                $category = Category::create([
                    'name' => $def['name'],
                    'description' => $def['description'] ?? null,
                    'parent_id' => $parent?->id,
                    'color' => $def['color'] ?? null,
                    'order' => $def['order'] ?? 0,
                    'is_active' => (bool) ($def['is_active'] ?? true),
                ]);
            }

            if ($category) {
                $ids[] = $category->id;
            } else {
                $skipped[] = $name;
            }
        }

        return $ids;
    }

    private function resolveTags(array $names): array
    {
        $ids = [];

        foreach ($names as $name) {
            $tag = Tag::whereRaw('LOWER(name) = ?', [$this->key($name)])->first()
                ?? Tag::create(['name' => trim($name), 'is_active' => true]);

            $ids[] = $tag->id;
        }

        return array_values(array_unique($ids));
    }

    private function lessonFor(?string $title, array $lessonsByTitle): ?Lesson
    {
        return $title ? ($lessonsByTitle[$this->key($title)] ?? null) : null;
    }

    private function crossCheck(array $data, bool $canCreateCategories): array
    {
        $errors = [];
        $warnings = [];

        // Peta judul lesson -> daftar tipe (lebih dari satu entri = judul ganda = ambigu).
        $lessonTypes = [];
        foreach (($data['sections'] ?? []) as $section) {
            foreach (($section['lessons'] ?? []) as $lesson) {
                $lessonTypes[$this->key($lesson['title'])][] = $lesson['type'];
            }
        }

        $slots = [];
        foreach (($data['quizzes'] ?? []) as $i => $quiz) {
            $label = "quizzes[$i] \"{$quiz['title']}\"";

            if (!empty($quiz['lesson_title'])) {
                $key = $this->key($quiz['lesson_title']);

                if (!isset($lessonTypes[$key])) {
                    $errors[] = "$label: lesson \"{$quiz['lesson_title']}\" tidak ditemukan di sections.";
                } elseif (count($lessonTypes[$key]) > 1) {
                    $errors[] = "$label: judul lesson \"{$quiz['lesson_title']}\" dipakai lebih dari satu lesson, ubah supaya unik.";
                } else {
                    if ($lessonTypes[$key][0] !== 'quiz') {
                        $warnings[] = "$label: lesson \"{$quiz['lesson_title']}\" bertipe \"{$lessonTypes[$key][0]}\", bukan \"quiz\".";
                    }

                    $slot = $key . '|' . ($quiz['quiz_type'] ?? 'practice');
                    if (isset($slots[$slot])) {
                        $errors[] = "$label: lesson ini sudah punya kuis bertipe \"" . ($quiz['quiz_type'] ?? 'practice') . '" (satu lesson maksimal satu kuis per tipe).';
                    }
                    $slots[$slot] = true;
                }
            }

            foreach (($quiz['questions'] ?? []) as $q => $question) {
                $qLabel = "$label soal " . ($q + 1);

                if ($question['type'] === 'multiple_choice') {
                    $options = $question['options'] ?? [];
                    $correct = collect($options)->filter(fn ($o) => !empty($o['is_correct']))->count();

                    if (count($options) < 2) {
                        $errors[] = "$qLabel: pilihan ganda butuh minimal 2 opsi.";
                    }
                    if ($correct !== 1) {
                        $errors[] = "$qLabel: harus ada tepat 1 opsi is_correct=true (ditemukan $correct).";
                    }
                } elseif ($question['type'] === 'true_false' && !in_array($question['correct_answer'] ?? null, ['true', 'false'], true)) {
                    $errors[] = "$qLabel: soal benar/salah wajib punya correct_answer \"true\" atau \"false\".";
                }
            }

            if (!empty($quiz['is_published'])) {
                $warnings[] = "$label: is_published diabaikan, kuis dibuat belum terbit.";
            }
        }

        foreach (($data['assignments'] ?? []) as $i => $item) {
            $label = "assignments[$i] \"{$item['title']}\"";

            if (!empty($item['lesson_title'])) {
                $key = $this->key($item['lesson_title']);

                if (!isset($lessonTypes[$key])) {
                    $errors[] = "$label: lesson \"{$item['lesson_title']}\" tidak ditemukan di sections.";
                } elseif (count($lessonTypes[$key]) > 1) {
                    $errors[] = "$label: judul lesson \"{$item['lesson_title']}\" dipakai lebih dari satu lesson, ubah supaya unik.";
                } elseif ($lessonTypes[$key][0] !== 'assignment') {
                    $warnings[] = "$label: lesson \"{$item['lesson_title']}\" bertipe \"{$lessonTypes[$key][0]}\", bukan \"assignment\".";
                }
            }

            if ($item['passing_score'] > $item['max_score']) {
                $errors[] = "$label: passing_score tidak boleh lebih besar dari max_score.";
            }

            $rubricTotal = collect($item['rubric'] ?? [])->sum('max_points');
            if ($rubricTotal > 0 && $rubricTotal !== $item['max_score']) {
                $warnings[] = "$label: total poin rubrik ($rubricTotal) tidak sama dengan max_score ({$item['max_score']}).";
            }

            if (!empty($item['is_published'])) {
                $warnings[] = "$label: is_published diabaikan, tugas dibuat belum terbit.";
            }
        }

        $course = $data['course'];

        if (($course['status'] ?? 'draft') !== 'draft') {
            $warnings[] = 'course.status diabaikan, course selalu dibuat sebagai draft.';
        }

        $declared = collect($data['new_categories'] ?? [])->map(fn ($c) => $this->key($c['name']))->all();
        foreach (($course['categories'] ?? []) as $name) {
            $exists = Category::whereRaw('LOWER(name) = ?', [$this->key($name)])->exists();

            if (!$exists && !($canCreateCategories && in_array($this->key($name), $declared, true))) {
                $warnings[] = "Kategori \"$name\" belum ada" . ($canCreateCategories
                    ? ' dan tidak ada di new_categories, jadi dilewati.'
                    : ' dan hanya admin yang dapat membuat kategori baru, jadi dilewati. Minta admin membuatnya lalu pilih manual.');
            }
        }

        $minutes = collect($data['sections'] ?? [])->sum(
            fn ($s) => collect($s['lessons'] ?? [])->sum(fn ($l) => $l['duration'] ?? 0)
        );
        $hours = (int) ceil($minutes / 60);
        if ($minutes > 0 && abs(($course['duration_total'] ?? 0) - $hours) > 1) {
            $warnings[] = "duration_total ({$course['duration_total']} jam) berbeda dari total durasi lesson (sekitar $hours jam).";
        }

        $videoWithoutUrl = collect($data['sections'] ?? [])->sum(
            fn ($s) => collect($s['lessons'] ?? [])->filter(fn ($l) => $l['type'] === 'video' && empty($l['video_url']))->count()
        );
        if ($videoWithoutUrl > 0) {
            $warnings[] = "$videoWithoutUrl lesson video belum punya video_url (isi manual setelah impor).";
        }

        return [$errors, $warnings];
    }

    private function rules(): array
    {
        $bool = 'nullable|boolean';

        return [
            'course' => 'required|array',
            'course.title' => 'required|string|max:255',
            'course.slug' => 'nullable|string|max:255|unique:courses,slug',
            'course.short_description' => 'nullable|string|max:255',
            'course.description' => 'required|string',
            'course.requirements' => 'nullable|array',
            'course.requirements.*' => 'nullable|string|max:255',
            'course.learning_objectives' => 'nullable|array',
            'course.learning_objectives.*' => 'nullable|string|max:255',
            'course.target_audience' => 'nullable|array',
            'course.target_audience.*' => 'nullable|string|max:255',
            'course.prerequisites' => 'nullable|array',
            'course.prerequisites.*' => 'nullable|string|max:255',
            'course.video_promo' => 'nullable|url',
            'course.price' => 'required|numeric|min:0',
            'course.sale_price' => 'nullable|numeric|min:0|lt:course.price',
            'course.sale_starts_at' => 'nullable|date',
            'course.sale_ends_at' => 'nullable|date|after:course.sale_starts_at',
            'course.level' => 'required|in:beginner,intermediate,advanced,all_levels',
            'course.language' => 'required|string|max:10',
            'course.duration_total' => 'nullable|integer|min:0',
            'course.status' => 'nullable|in:draft,pending,published,archived',
            'course.is_featured' => $bool,
            'course.has_certificate' => $bool,
            'course.max_students' => 'nullable|integer|min:0',
            'course.enrollment_end_date' => 'nullable|date',
            'course.start_date' => 'nullable|date',
            'course.end_date' => 'nullable|date|after:course.start_date',
            'course.access_days' => 'nullable|integer|min:0',
            'course.meta_keywords' => 'nullable|string|max:255',
            'course.meta_description' => 'nullable|string',
            'course.categories' => 'nullable|array',
            'course.categories.*' => 'string|max:255',
            'course.tags' => 'nullable|array',
            'course.tags.*' => 'string|max:255',

            'new_categories' => 'nullable|array',
            'new_categories.*.name' => 'required|string|max:255',
            'new_categories.*.description' => 'nullable|string',
            'new_categories.*.parent' => 'nullable|string|max:255',
            'new_categories.*.color' => 'nullable|string|max:7',
            'new_categories.*.order' => 'nullable|integer',
            'new_categories.*.is_active' => $bool,
            'new_tags' => 'nullable|array',
            'new_tags.*.name' => 'required|string|max:255',

            'sections' => 'nullable|array',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.description' => 'nullable|string',
            'sections.*.is_published' => $bool,
            'sections.*.lessons' => 'nullable|array',
            'sections.*.lessons.*.title' => 'required|string|max:255',
            'sections.*.lessons.*.type' => 'required|in:video,article,quiz,assignment,live,discussion',
            'sections.*.lessons.*.content' => 'nullable|string',
            'sections.*.lessons.*.duration' => 'nullable|integer|min:0',
            'sections.*.lessons.*.video_url' => 'nullable|url',
            'sections.*.lessons.*.is_free_preview' => $bool,
            'sections.*.lessons.*.points' => 'nullable|integer|min:0',
            'sections.*.lessons.*.status' => 'nullable|in:draft,published',
            'sections.*.lessons.*.resources' => 'nullable|array',
            'sections.*.lessons.*.resources.*.title' => 'required|string|max:255',
            'sections.*.lessons.*.resources.*.description' => 'nullable|string',

            'quizzes' => 'nullable|array',
            'quizzes.*.title' => 'required|string|max:255',
            'quizzes.*.lesson_title' => 'nullable|string|max:255',
            'quizzes.*.description' => 'nullable|string',
            'quizzes.*.quiz_type' => 'nullable|in:' . self::QUIZ_TYPES,
            'quizzes.*.time_limit' => 'nullable|integer|min:0',
            'quizzes.*.attempts_allowed' => 'nullable|integer|min:1',
            'quizzes.*.passing_score' => 'nullable|integer|min:0|max:100',
            'quizzes.*.randomize_questions' => $bool,
            'quizzes.*.is_published' => $bool,
            'quizzes.*.questions' => 'nullable|array',
            'quizzes.*.questions.*.question' => 'required|string',
            'quizzes.*.questions.*.type' => 'required|in:multiple_choice,true_false,essay',
            'quizzes.*.questions.*.points' => 'required|integer|min:1',
            'quizzes.*.questions.*.explanation' => 'nullable|string',
            'quizzes.*.questions.*.correct_answer' => 'nullable|in:true,false',
            'quizzes.*.questions.*.options' => 'nullable|array',
            'quizzes.*.questions.*.options.*.text' => 'required|string',
            'quizzes.*.questions.*.options.*.is_correct' => $bool,
            'quizzes.*.questions.*.options.*.points' => 'nullable|integer|min:0',

            'assignments' => 'nullable|array',
            'assignments.*.title' => 'required|string|max:255',
            'assignments.*.lesson_title' => 'nullable|string|max:255',
            'assignments.*.description' => 'required|string',
            'assignments.*.instructions' => 'nullable|string',
            'assignments.*.max_score' => 'required|integer|min:1',
            'assignments.*.passing_score' => 'required|integer|min:0',
            'assignments.*.due_date' => 'nullable|date|after:now',
            'assignments.*.is_published' => $bool,
            'assignments.*.allow_late_submission' => $bool,
            'assignments.*.late_penalty' => 'nullable|integer|min:0|max:100',
            'assignments.*.rubric' => 'nullable|array',
            'assignments.*.rubric.*.criteria' => 'required|string|max:255',
            'assignments.*.rubric.*.description' => 'nullable|string|max:1000',
            'assignments.*.rubric.*.max_points' => 'required|integer|min:1',
        ];
    }

    /**
     * Samakan perilaku dengan form: string kosong dianggap tidak diisi (null), dan correct_answer
     * boolean (true/false) diubah ke string karena itu nilai yang dipakai form.
     */
    private function normalize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->normalize($value);
            } elseif ($value === '') {
                $data[$key] = null;
            } elseif ($key === 'correct_answer' && is_bool($value)) {
                $data[$key] = $value ? 'true' : 'false';
            }
        }

        return $data;
    }

    private function key(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    /** "sections.2.lessons.1.type" menjadi "sections[2].lessons[1].type". */
    private function label(string $key): string
    {
        return preg_replace('/\.(\d+)(?=\.|$)/', '[$1]', $key);
    }
}
