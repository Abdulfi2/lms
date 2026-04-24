<?php
// app/Http/Requests/Admin/UpdateCourseRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $course = $this->route('course');

        return $user->hasRole('admin') || ($user->hasRole('instructor') && $user->id === $course->instructor_id);
    }

    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('courses')->ignore($course->id)],
            'short_description' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|array',
            'learning_objectives' => 'nullable|array',
            'target_audience' => 'nullable|array',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'video_promo' => 'nullable|url',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'sale_starts_at' => 'nullable|date',
            'sale_ends_at' => 'nullable|date|after:sale_starts_at',
            'level' => 'required|in:beginner,intermediate,advanced,all_levels',
            'language' => 'required|string|max:10',
            'duration_total' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,pending,published,archived',
            'is_featured' => 'nullable|boolean',
            'has_certificate' => 'nullable|boolean',
            'max_students' => 'nullable|integer|min:0',
            'enrollment_end_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'access_days' => 'nullable|integer|min:0',
            'prerequisites' => 'nullable|array',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'instructor_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul kursus wajib diisi.',
            'description.required' => 'Deskripsi kursus wajib diisi.',
            'price.required' => 'Harga kursus wajib diisi.',
            'price.min' => 'Harga tidak boleh negatif.',
            'sale_price.lt' => 'Harga diskon harus lebih kecil dari harga normal.',
            'level.required' => 'Level kursus wajib dipilih.',
            'status.required' => 'Status kursus wajib dipilih.',
            'instructor_id.required' => 'Instruktur wajib dipilih.',
            'sale_ends_at.after' => 'Tanggal berakhir diskon harus setelah tanggal mulai diskon.',
            'end_date.after' => 'Tanggal berakhir kursus harus setelah tanggal mulai kursus.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Konversi checkbox values dari 'on' atau null ke boolean
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'has_certificate' => $this->boolean('has_certificate'),
        ]);

        // Jika slug kosong, generate dari title
        if ($this->filled('title') && empty($this->slug)) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title) . '-' . $this->route('course')->id,
            ]);
        }
    }
}