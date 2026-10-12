<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CourseImporter;
use Illuminate\Console\Command;
use InvalidArgumentException;

class ImportCourse extends Command
{
    protected $signature = 'course:import
                            {file : Path ke berkas JSON paket course}
                            {--instructor= : ID atau email instruktur pemilik course}
                            {--dry-run : Hanya validasi dan tampilkan ringkasan, tanpa menyimpan}';

    protected $description = 'Impor paket course (course, section, lesson, kuis, tugas) dari JSON buatan AI agent sebagai draft';

    public function handle(CourseImporter $importer): int
    {
        $path = $this->argument('file');

        if (!is_file($path)) {
            $this->error("Berkas tidak ditemukan: $path");
            return self::FAILURE;
        }

        try {
            $data = $importer->decode(file_get_contents($path));
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        // Perintah artisan dijalankan operator server, jadi boleh membuat kategori baru seperti admin.
        $result = $importer->validate($data, true);

        $this->table(['Isi paket', 'Jumlah'], collect($result['summary'])
            ->except('title')->map(fn ($v, $k) => [$k, $v])->values()->all());
        $this->line('Course: ' . $result['summary']['title']);

        foreach ($result['warnings'] as $warning) {
            $this->warn('Peringatan: ' . $warning);
        }

        if ($result['errors']) {
            foreach ($result['errors'] as $error) {
                $this->error($error);
            }
            $this->error(count($result['errors']) . ' kesalahan. Tidak ada yang disimpan.');
            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run: paket valid, tidak ada yang disimpan.');
            return self::SUCCESS;
        }

        $instructor = $this->resolveInstructor();
        if (!$instructor) {
            return self::FAILURE;
        }

        $report = $importer->import($data, $instructor, true);

        $this->info("Course dibuat sebagai draft (ID {$report['course']->id}) untuk {$instructor->name}.");

        foreach ($report['skipped_categories'] as $name) {
            $this->warn("Kategori \"$name\" dilewati.");
        }

        if ($report['pending_uploads']) {
            $this->line('Lampiran yang perlu diunggah manual:');
            foreach ($report['pending_uploads'] as $item) {
                $this->line("  - $item");
            }
        }

        return self::SUCCESS;
    }

    private function resolveInstructor(): ?User
    {
        $value = $this->option('instructor');

        if (!$value) {
            $this->error('Opsi --instructor (ID atau email) wajib diisi.');
            return null;
        }

        $user = User::where('id', $value)->orWhere('email', $value)->first();

        if (!$user || !$user->hasRole('instructor')) {
            $this->error("Pengguna \"$value\" tidak ditemukan atau bukan instruktur.");
            return null;
        }

        return $user;
    }
}
