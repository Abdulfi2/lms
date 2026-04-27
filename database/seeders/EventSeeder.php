<?php
// database/seeders/EventSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user organizer (admin atau event manager)
        $organizer = User::where('default_role', 'admin')->first();

        if (!$organizer) {
            $organizer = User::first();
        }

        $events = [
            [
                'title' => 'Webinar: Pengembangan Karir di Era Digital',
                'description' => 'Webinar ini akan membahas strategi pengembangan karir di era digital, termasuk keterampilan yang dibutuhkan, tips membangun personal branding, dan peluang kerja di bidang teknologi.',
                'type' => 'webinar',
                'category' => 'education',
                'speaker' => 'Dr. Budi Santoso, M.Kom',
                'speaker_bio' => 'Pakar pengembangan karir dengan pengalaman 15 tahun di industri teknologi',
                'speaker_photo' => null,
                'location' => 'Zoom Meeting (Link akan dikirim via email)',
                'zoom_link' => 'https://zoom.us/j/123456789?pwd=example',
                'meeting_id' => '123-456-789',
                'passcode' => 'webinar2024',
                'start_time' => Carbon::now()->addDays(7)->setTime(10, 0, 0),
                'end_time' => Carbon::now()->addDays(7)->setTime(12, 0, 0),
                'max_participants' => 500,
                'price_type' => 'free',
                'price' => 0,
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Workshop: Fullstack Web Development dengan Laravel',
                'description' => 'Workshop intensif 2 hari untuk belajar membangun aplikasi web modern menggunakan Laravel 10 dan Vue.js 3. Peserta akan membuat project portfolio website.',
                'type' => 'workshop',
                'category' => 'technology',
                'speaker' => 'John Doe',
                'speaker_bio' => 'Senior Fullstack Developer dengan pengalaman 8 tahun',
                'speaker_photo' => null,
                'location' => 'Gedung Teknologi ITB, Bandung',
                'zoom_link' => null,
                'meeting_id' => null,
                'passcode' => null,
                'start_time' => Carbon::now()->addDays(14)->setTime(9, 0, 0),
                'end_time' => Carbon::now()->addDays(15)->setTime(17, 0, 0),
                'max_participants' => 50,
                'price_type' => 'paid',
                'price' => 350000,
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Parenting di Era Digital: Tips Mendampingi Anak Belajar Online',
                'description' => 'Webinar khusus untuk orang tua tentang bagaimana mendampingi anak belajar online dengan efektif, mengatur screen time, dan membangun komunikasi positif.',
                'type' => 'parenting',
                'category' => 'parenting',
                'speaker' => 'Dr. Sarah Wijaya, M.Psi',
                'speaker_bio' => 'Psikolog anak dan keluarga',
                'speaker_photo' => null,
                'location' => 'Online via Google Meet',
                'zoom_link' => 'https://meet.google.com/abc-defg-hij',
                'meeting_id' => 'abc-defg-hij',
                'passcode' => null,
                'start_time' => Carbon::now()->addDays(10)->setTime(19, 0, 0),
                'end_time' => Carbon::now()->addDays(10)->setTime(20, 30, 0),
                'max_participants' => 300,
                'price_type' => 'free',
                'price' => 0,
                'is_featured' => false,
                'status' => 'published',
            ],
            [
                'title' => 'Seminar Nasional: Transformasi Digital Pendidikan',
                'description' => 'Seminar nasional yang menghadirkan para ahli pendidikan dan teknologi untuk membahas masa depan pendidikan digital di Indonesia.',
                'type' => 'seminar',
                'category' => 'education',
                'speaker' => 'Prof. Dr. Ahmad Rizali, M.Sc',
                'speaker_bio' => 'Guru Besar Teknologi Pendidikan',
                'speaker_photo' => null,
                'location' => 'Auditorium Universitas Indonesia, Depok',
                'zoom_link' => null,
                'meeting_id' => null,
                'passcode' => null,
                'start_time' => Carbon::now()->addDays(21)->setTime(8, 0, 0),
                'end_time' => Carbon::now()->addDays(21)->setTime(16, 0, 0),
                'max_participants' => 1000,
                'price_type' => 'free',
                'price' => 0,
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Live Class: Data Science dengan Python',
                'description' => 'Kelas live interaktif untuk mempelajari dasar-dasar Data Science menggunakan Python, termasuk Pandas, NumPy, dan visualisasi data.',
                'type' => 'live_class',
                'category' => 'technology',
                'speaker' => 'Maya Sari, M.Kom',
                'speaker_bio' => 'Data Scientist di perusahaan startup unicorn',
                'speaker_photo' => null,
                'location' => 'Zoom Meeting (Link akan dikirim)',
                'zoom_link' => 'https://zoom.us/j/987654321',
                'meeting_id' => '987-654-321',
                'passcode' => 'datascience',
                'start_time' => Carbon::now()->addDays(5)->setTime(15, 0, 0),
                'end_time' => Carbon::now()->addDays(5)->setTime(17, 0, 0),
                'max_participants' => 200,
                'price_type' => 'paid',
                'price' => 150000,
                'is_featured' => false,
                'status' => 'published',
            ],
            [
                'title' => 'Workshop: UI/UX Design untuk Pemula',
                'description' => 'Pelajari dasar-dasar UI/UX design, prinsip desain, dan praktik menggunakan Figma. Peserta akan membuat prototype aplikasi mobile.',
                'type' => 'workshop',
                'category' => 'technology',
                'speaker' => 'Andi Pratama',
                'speaker_bio' => 'UI/UX Designer dengan pengalaman 6 tahun',
                'speaker_photo' => null,
                'location' => 'Online via Zoom',
                'zoom_link' => 'https://zoom.us/j/555555555',
                'meeting_id' => '555-555-555',
                'passcode' => 'uiux2024',
                'start_time' => Carbon::now()->addDays(28)->setTime(9, 0, 0),
                'end_time' => Carbon::now()->addDays(28)->setTime(16, 0, 0),
                'max_participants' => 100,
                'price_type' => 'paid',
                'price' => 250000,
                'is_featured' => false,
                'status' => 'draft',
            ],
            [
                'title' => 'Webinar: Kesehatan Mental di Tempat Kerja',
                'description' => 'Webinar tentang pentingnya kesehatan mental di lingkungan kerja, cara mengelola stres, dan menciptakan work-life balance yang sehat.',
                'type' => 'webinar',
                'category' => 'health',
                'speaker' => 'Dr. Rina Fitriani',
                'speaker_bio' => 'Psikolog klinis dan konselor perusahaan',
                'speaker_photo' => null,
                'location' => 'Online via Zoom',
                'zoom_link' => 'https://zoom.us/j/444444444',
                'meeting_id' => '444-444-444',
                'passcode' => 'mentalhealth',
                'start_time' => Carbon::now()->addDays(18)->setTime(14, 0, 0),
                'end_time' => Carbon::now()->addDays(18)->setTime(15, 30, 0),
                'max_participants' => 500,
                'price_type' => 'free',
                'price' => 0,
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Seminar Bisnis: Cara Memulai Usaha Digital',
                'description' => 'Seminar praktis untuk memulai bisnis digital dari nol, termasuk strategi marketing online, manajemen keuangan, dan legalitas usaha.',
                'type' => 'seminar',
                'category' => 'business',
                'speaker' => 'Andi Wijaya, S.E., M.M.',
                'speaker_bio' => 'Praktisi bisnis dan founder startup e-commerce',
                'speaker_photo' => null,
                'location' => 'Gedung Graha Pena, Surabaya',
                'zoom_link' => null,
                'meeting_id' => null,
                'passcode' => null,
                'start_time' => Carbon::now()->addDays(35)->setTime(9, 0, 0),
                'end_time' => Carbon::now()->addDays(35)->setTime(17, 0, 0),
                'max_participants' => 200,
                'price_type' => 'paid',
                'price' => 500000,
                'is_featured' => false,
                'status' => 'published',
            ],
        ];

        foreach ($events as $eventData) {
            // Generate slug unik
            $slug = Str::slug($eventData['title']) . '-' . uniqid();

            Event::create([
                'organizer_id' => $organizer->id,
                'title' => $eventData['title'],
                'slug' => $slug,
                'description' => $eventData['description'],
                'type' => $eventData['type'],
                'category' => $eventData['category'],
                'speaker' => $eventData['speaker'],
                'speaker_bio' => $eventData['speaker_bio'],
                'speaker_photo' => $eventData['speaker_photo'],
                'location' => $eventData['location'],
                'zoom_link' => $eventData['zoom_link'],
                'meeting_id' => $eventData['meeting_id'],
                'passcode' => $eventData['passcode'],
                'start_time' => $eventData['start_time'],
                'end_time' => $eventData['end_time'],
                'max_participants' => $eventData['max_participants'],
                'price_type' => $eventData['price_type'],
                'price' => $eventData['price'],
                'is_featured' => $eventData['is_featured'],
                'status' => $eventData['status'],
                'total_registrations' => 0,
            ]);
        }

        $this->command->info('EventSeeder: ' . count($events) . ' events created successfully.');
    }
}