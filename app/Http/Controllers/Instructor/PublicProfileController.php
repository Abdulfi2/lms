<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    /**
     * Form edit profil publik instruktur (bio, keahlian, tautan sosial).
     */
    public function edit()
    {
        // approval_status wajib 'approved' — reaching this controller already melewati middleware
        // 'instructor.approved', jadi baris profil baru tidak boleh jatuh ke default migrasi ('pending')
        // yang akan mengunci instruktur ini keluar dari seluruh area instruktur pada request berikutnya.
        $profile = Profile::firstOrCreate(
            ['profileable_id' => auth()->id(), 'profileable_type' => \App\Models\User::class],
            ['profile_type' => 'instructor', 'approval_status' => 'approved']
        );

        return view('instructor.public-profile.edit', compact('profile'));
    }

    /**
     * Simpan profil publik instruktur.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:2000',
            'expertise' => 'nullable|array',
            'expertise.*' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'is_public' => 'nullable|boolean',
        ]);

        // approval_status wajib 'approved' — reaching this controller already melewati middleware
        // 'instructor.approved', jadi baris profil baru tidak boleh jatuh ke default migrasi ('pending')
        // yang akan mengunci instruktur ini keluar dari seluruh area instruktur pada request berikutnya.
        $profile = Profile::firstOrCreate(
            ['profileable_id' => auth()->id(), 'profileable_type' => \App\Models\User::class],
            ['profile_type' => 'instructor', 'approval_status' => 'approved']
        );

        $profile->update([
            'professional_info' => [
                'headline' => $validated['headline'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'expertise' => array_values(array_filter($validated['expertise'] ?? [])),
            ],
            'social_media' => [
                'website' => $validated['website'] ?? null,
                'linkedin' => $validated['linkedin'] ?? null,
                'twitter' => $validated['twitter'] ?? null,
                'instagram' => $validated['instagram'] ?? null,
                'youtube' => $validated['youtube'] ?? null,
            ],
            'is_public' => $request->boolean('is_public'),
        ]);

        return back()->with('success', 'Profil publik berhasil diperbarui.');
    }
}
