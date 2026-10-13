<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Event;
use App\Models\KnowledgeBaseArticle;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Isian panjang yang sekarang memakai TinyMCE: form harus merender editor (bukan textarea polos),
 * dan halaman tampil harus aman untuk data lama (teks polos) maupun HTML baru.
 */
class RichTextFormsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach (['admin', 'instructor', 'student', 'support', 'event_manager'] as $role) {
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
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->assignRole($role);

        return $user;
    }

    private function event(string $description): Event
    {
        return Event::create([
            'organizer_id' => $this->user('admin')->id,
            'title' => 'Event Uji ' . uniqid(),
            'description' => $description,
            'type' => 'webinar',
            'category' => 'education',
            'start_time' => now()->addWeek(),
            'status' => 'published',
        ]);
    }

    public function test_admin_event_forms_render_tinymce(): void
    {
        $admin = $this->user('admin');
        $event = $this->event('<p>Isi</p>');

        $this->actingAs($admin)->get(route('admin.events.create'))
            ->assertOk()
            ->assertSee('id="tinymce-', false)
            ->assertSee('name="description"', false)
            ->assertDontSee('name="description" rows="4"', false);

        $this->actingAs($admin)->get(route('admin.events.edit', $event))
            ->assertOk()
            ->assertSee('id="tinymce-', false)
            ->assertSee('&lt;p&gt;Isi&lt;/p&gt;', false);
    }

    public function test_admin_course_and_lesson_forms_bind_editor_to_alpine(): void
    {
        $admin = $this->user('admin');
        $instructor = $this->user('instructor');
        $course = Course::create([
            'title' => 'Course Uji ' . uniqid(), 'description' => '<p>Deskripsi `kutip` "ganda" ${x}</p>',
            'price' => 0, 'level' => 'beginner', 'language' => 'id', 'status' => 'draft', 'instructor_id' => $instructor->id,
        ]);
        $section = Section::create(['course_id' => $course->id, 'title' => 'S1', 'order' => 1]);

        $this->actingAs($admin)->get(route('admin.courses.create'))
            ->assertOk()
            ->assertSee('id="tinymce-', false)
            ->assertSee('x-model="form.description"', false);

        $edit = $this->actingAs($admin)->get(route('admin.courses.edit', $course))->assertOk();
        $edit->assertSee('x-model="form.description"', false);
        // Nilai awal masuk lewat @json (aman untuk kutip/backtick/${}), bukan template literal.
        $edit->assertSee('Deskripsi `kutip`', false)->assertDontSee('description: `', false);

        $this->actingAs($admin)->get(route('admin.lessons.create', [$course, $section]))
            ->assertOk()
            ->assertSee('id="tinymce-', false)
            ->assertSee('x-model="form.content"', false);
    }

    public function test_knowledge_base_forms_render_tinymce(): void
    {
        $support = $this->user('support');
        $article = KnowledgeBaseArticle::create([
            'user_id' => $support->id, 'title' => 'KB Uji', 'slug' => 'kb-uji-' . uniqid(),
            'category' => 'Umum', 'content' => '<p>Isi KB</p>', 'is_published' => true,
        ]);

        $this->actingAs($support)->get(route('support.knowledge-base.create'))
            ->assertOk()->assertSee('id="tinymce-', false);
        $this->actingAs($support)->get(route('support.knowledge-base.edit', $article))
            ->assertOk()->assertSee('id="tinymce-', false);
    }

    public function test_event_page_shows_legacy_plain_text_and_new_html_safely(): void
    {
        $legacy = $this->event("Baris satu\nHadiah 5 < 10 & gratis");
        $this->get(route('events.show', $legacy->slug))
            ->assertOk()
            ->assertSee("Baris satu<br />", false)
            ->assertSee('Hadiah 5 &lt; 10 &amp; gratis', false);

        $html = $this->event('<h2>Agenda</h2><p onclick="x()">Hari <strong>pertama</strong></p><script>alert(1)</script>');
        $this->get(route('events.show', $html->slug))
            ->assertOk()
            ->assertSee('<h2>Agenda</h2>', false)
            ->assertSee('<strong>pertama</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('onclick="x()"', false);
    }

    public function test_event_lists_show_plain_excerpt_without_tags(): void
    {
        $this->event('<p>Ringkasan <strong>penting</strong> acara</p>');

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee('Ringkasan penting acara')
            ->assertDontSee('&lt;strong&gt;', false);
    }

    public function test_knowledge_base_article_renders_html_and_legacy_text(): void
    {
        $support = $this->user('support');
        $make = fn (string $content) => KnowledgeBaseArticle::create([
            'user_id' => $support->id, 'title' => 'KB ' . uniqid(), 'slug' => 'kb-' . uniqid(),
            'category' => 'Umum', 'content' => $content, 'is_published' => true,
        ]);

        $this->actingAs($support)->get(route('knowledge-base.show', $make('<ol><li>Langkah 1</li></ol>')))
            ->assertOk()->assertSee('<ol><li>Langkah 1</li></ol>', false);

        $this->actingAs($support)->get(route('knowledge-base.show', $make("Satu\nDua")))
            ->assertOk()->assertSee("Satu<br />", false);
    }
}
