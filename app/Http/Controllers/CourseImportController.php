<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CourseImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Impor paket course dari JSON buatan AI agent. Dipakai bersama oleh admin dan instruktur
 * (route prefix admin. / instructor.); bedanya: admin memilih instruktur pemilik course dan
 * boleh membuat kategori baru, instruktur selalu menjadi pemilik course-nya sendiri.
 */
class CourseImportController extends Controller
{
    private const DISK_DIR = 'course-imports';

    public function __construct(private CourseImporter $importer)
    {
    }

    public function create()
    {
        return view('course-import.create', $this->context());
    }

    /**
     * Validasi paket dan tampilkan ringkasan. Paket yang lolos disimpan sementara di storage
     * (bukan di form) supaya tidak bisa diubah klien antara pratinjau dan simpan.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'nullable|file|max:2048',
            'json' => 'nullable|string|max:2000000',
        ]);

        if (!$request->hasFile('file') && !filled($request->input('json'))) {
            return back()->withErrors(['file' => 'Pilih berkas JSON atau tempel isi JSON terlebih dahulu.']);
        }

        $raw = $request->hasFile('file')
            ? file_get_contents($request->file('file')->getRealPath())
            : $request->input('json');

        try {
            $data = $this->importer->decode($raw);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['file' => $e->getMessage()])->withInput($request->only('json'));
        }

        $result = $this->importer->validate($data, $this->isAdmin());

        $token = null;
        if (!$result['errors']) {
            $token = (string) Str::uuid();
            Storage::disk('local')->put($this->path($token), json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        return view('course-import.preview', $this->context() + compact('result', 'token'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|uuid',
            'instructor_id' => $this->isAdmin() ? 'required|exists:users,id' : 'nullable',
        ]);

        $instructor = $this->isAdmin() ? User::find($validated['instructor_id']) : auth()->user();

        if (!$instructor || !$instructor->hasRole('instructor')) {
            return back()->withErrors(['instructor_id' => 'Pengguna yang dipilih bukan instruktur.']);
        }

        $path = $this->path($validated['token']);

        if (!Storage::disk('local')->exists($path)) {
            return redirect()->route($this->prefix() . '.courses.import.create')
                ->withErrors(['file' => 'Sesi impor sudah kedaluwarsa atau sudah diproses. Unggah ulang berkasnya.']);
        }

        $data = json_decode(Storage::disk('local')->get($path), true);

        // Validasi ulang: kategori, slug, atau tanggal bisa berubah sejak pratinjau.
        $result = $this->importer->validate($data, $this->isAdmin());
        if ($result['errors']) {
            Storage::disk('local')->delete($path);

            return redirect()->route($this->prefix() . '.courses.import.create')
                ->withErrors(['file' => 'Paket tidak lagi valid: ' . implode(' ', $result['errors'])]);
        }

        try {
            $report = $this->importer->import($data, $instructor, $this->isAdmin());
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route($this->prefix() . '.courses.import.create')
                ->withErrors(['file' => 'Impor gagal dan tidak ada yang disimpan. Detail ada di log aplikasi.']);
        }

        Storage::disk('local')->delete($path);

        return view('course-import.done', $this->context() + [
            'report' => $report,
            'warnings' => $result['warnings'],
            'instructor' => $instructor,
        ]);
    }

    private function context(): array
    {
        return [
            'prefix' => $this->prefix(),
            'isAdmin' => $this->isAdmin(),
            'instructors' => $this->isAdmin()
                ? User::role('instructor')->orderBy('name')->get(['id', 'name', 'email'])
                : collect(),
        ];
    }

    private function prefix(): string
    {
        return $this->isAdmin() ? 'admin' : 'instructor';
    }

    private function isAdmin(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    private function path(string $token): string
    {
        return self::DISK_DIR . '/' . auth()->id() . '-' . $token . '.json';
    }
}
