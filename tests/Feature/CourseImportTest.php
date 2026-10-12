<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Memakai DatabaseTransactions (bukan RefreshDatabase) supaya tiap test di-rollback tanpa
 * menghapus isi database; membutuhkan database yang sudah termigrasi.
 */
class CourseImportTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['admin', 'instructor', 'student'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    private function user(string $role): User
    {
        $user = User::create([
            'name' => ucfirst($role) . ' Uji',
            'email' => $role . '.' . uniqid() . '@example.test',
            'password' => bcrypt('password'),
            'default_role' => $role,
            'is_active' => true,
        ]);
        // email_verified_at tidak ada di $fillable User, jadi harus di-set langsung agar lolos middleware 'verified'.
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->assignRole($role);

        return $user;
    }

    private string $suffix = '';

    private function package(): array
    {
        $this->suffix = $this->suffix ?: ' ' . uniqid();

        $data = json_decode(file_get_contents(base_path('tests/Fixtures/course-package.json')), true);
        // Jadwal tugas dibuat relatif supaya fixture tidak basi (due_date harus setelah sekarang).
        $data['assignments'][0]['due_date'] = now()->addYear()->format('Y-m-d H:i:s');

        // Judul, kategori, dan tag dibuat unik per test supaya tidak bergantung pada isi database.
        $data['course']['title'] .= $this->suffix;
        $data['course']['categories'] = [$data['course']['categories'][0] . $this->suffix];
        $data['new_categories'][0]['name'] .= $this->suffix;
        $data['course']['tags'] = array_map(fn ($t) => $t . $this->suffix, $data['course']['tags']);

        return $data;
    }

    private function title(): string
    {
        return 'Kursus Uji Impor Paket JSON' . $this->suffix;
    }

    private function categoryName(): string
    {
        return 'Pelatihan Amil (Uji Impor)' . $this->suffix;
    }

    private function upload(array $package): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('paket.json', json_encode($package));
    }

    private function tokenFrom($response): string
    {
        preg_match('/name="token" value="([0-9a-f-]{36})"/', $response->getContent(), $m);
        $this->assertNotEmpty($m, 'Token pratinjau tidak ditemukan di halaman.');

        return $m[1];
    }

    public function test_student_cannot_open_import_page(): void
    {
        $this->actingAs($this->user('student'))
            ->get(route('instructor.courses.import.create'))
            ->assertForbidden();
    }

    public function test_instructor_can_open_import_page(): void
    {
        $this->actingAs($this->user('instructor'))
            ->get(route('instructor.courses.import.create'))
            ->assertOk()
            ->assertSee('Impor Kursus dari JSON');
    }

    public function test_instructor_previews_and_imports_package_as_draft(): void
    {
        $instructor = $this->user('instructor');
        $this->actingAs($instructor);

        $preview = $this->post(route('instructor.courses.import.preview'), ['file' => $this->upload($this->package())])
            ->assertOk()
            ->assertSee('Kursus Uji Impor Paket JSON')
            ->assertSee('Buat sebagai draft');

        $this->assertSame(0, Course::where('title', $this->title())->count(), 'Pratinjau tidak boleh menyimpan apa pun.');

        $this->post(route('instructor.courses.import.store'), ['token' => $this->tokenFrom($preview)])
            ->assertOk()
            ->assertSee('Impor Selesai');

        $course = Course::where('title', $this->title())->firstOrFail();
        $this->assertSame('draft', $course->status);
        $this->assertSame($instructor->id, $course->instructor_id);
        $this->assertSame(2, $course->total_sections);
        $this->assertSame(5, $course->total_lessons);
        $this->assertSame([2, 3], $course->sections()->orderBy('order')->pluck('total_lessons')->all());

        $this->assertSame(2, Quiz::where('course_id', $course->id)->count());
        $this->assertSame(0, Quiz::where('course_id', $course->id)->where('is_published', true)->count());
        $pretest = Quiz::where('course_id', $course->id)->where('quiz_type', 'pretest')->firstOrFail();
        $this->assertSame('Pretest orientasi', $pretest->lesson->title);
        $this->assertSame(3, $pretest->questions()->count());

        $assignment = Assignment::where('course_id', $course->id)->firstOrFail();
        $this->assertFalse((bool) $assignment->is_published);
        $this->assertSame(100, (int) \App\Models\AssignmentRubric::where('assignment_id', $assignment->id)->sum('max_points'));

        $this->assertTrue(Tag::where('name', 'uji impor' . $this->suffix)->exists());
        $this->assertSame(2, $course->tags()->count());
    }

    public function test_instructor_cannot_create_new_categories(): void
    {
        $instructor = $this->user('instructor');
        $this->actingAs($instructor);

        $preview = $this->post(route('instructor.courses.import.preview'), ['file' => $this->upload($this->package())])
            ->assertOk()
            ->assertSee('hanya admin yang dapat membuat kategori baru');

        $this->post(route('instructor.courses.import.store'), ['token' => $this->tokenFrom($preview)])->assertOk();

        $this->assertFalse(Category::where('name', $this->categoryName())->exists());
        $this->assertSame(0, Course::where('title', $this->title())->firstOrFail()->categories()->count());
    }

    public function test_admin_imports_for_chosen_instructor_and_creates_category(): void
    {
        $admin = $this->user('admin');
        $instructor = $this->user('instructor');
        $this->actingAs($admin);

        $preview = $this->post(route('admin.courses.import.preview'), ['file' => $this->upload($this->package())])->assertOk();

        // Admin wajib memilih instruktur.
        $this->post(route('admin.courses.import.store'), ['token' => $this->tokenFrom($preview)])
            ->assertSessionHasErrors('instructor_id');

        $this->post(route('admin.courses.import.store'), [
            'token' => $this->tokenFrom($preview),
            'instructor_id' => $instructor->id,
        ])->assertOk();

        $course = Course::where('title', $this->title())->firstOrFail();
        $this->assertSame($instructor->id, $course->instructor_id);
        $this->assertSame([$this->categoryName()], $course->categories()->pluck('name')->all());
    }

    public function test_invalid_package_shows_errors_and_saves_nothing(): void
    {
        $this->actingAs($this->user('instructor'));

        $package = $this->package();
        $package['course']['level'] = 'expert';
        $package['quizzes'][0]['questions'][0]['options'][1]['is_correct'] = true;
        $package['quizzes'][1]['lesson_title'] = 'Lesson Tidak Ada';

        $response = $this->post(route('instructor.courses.import.preview'), ['file' => $this->upload($package)])
            ->assertOk()
            ->assertSee('course.level')
            ->assertDontSee('Buat sebagai draft');

        $this->assertSame(0, Course::where('title', $this->title())->count());

        // Kesalahan struktur dibereskan; kesalahan pemeriksaan silang baru muncul setelahnya.
        $package['course']['level'] = 'beginner';
        $this->post(route('instructor.courses.import.preview'), ['file' => $this->upload($package)])
            ->assertOk()
            ->assertSee('tepat 1 opsi is_correct')
            ->assertSee('Lesson Tidak Ada')
            ->assertDontSee('Buat sebagai draft');
    }

    public function test_malformed_json_is_rejected(): void
    {
        $this->actingAs($this->user('instructor'))
            ->post(route('instructor.courses.import.preview'), [
                'file' => UploadedFile::fake()->createWithContent('rusak.json', '{ bukan json'),
            ])
            ->assertSessionHasErrors('file');
    }

    public function test_token_cannot_be_used_by_another_user_or_twice(): void
    {
        $owner = $this->user('instructor');
        $preview = $this->actingAs($owner)
            ->post(route('instructor.courses.import.preview'), ['file' => $this->upload($this->package())]);
        $token = $this->tokenFrom($preview);

        // Pengguna lain tidak bisa memakai token milik orang lain (path berisi id pemilik).
        $this->actingAs($this->user('instructor'))
            ->post(route('instructor.courses.import.store'), ['token' => $token])
            ->assertSessionHasErrors('file');
        $this->assertSame(0, Course::where('title', $this->title())->count());

        // Pemilik bisa memakainya sekali; pemakaian kedua ditolak.
        $this->actingAs($owner)->post(route('instructor.courses.import.store'), ['token' => $token])->assertOk();
        $this->actingAs($owner)->post(route('instructor.courses.import.store'), ['token' => $token])->assertSessionHasErrors('file');
        $this->assertSame(1, Course::where('title', $this->title())->count());
    }
}
