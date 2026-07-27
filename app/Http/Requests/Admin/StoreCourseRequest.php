<?php
// app/Http/Requests/Admin/StoreCourseRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('instructor');
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:courses,slug',
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
            'instructor_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (!\App\Models\User::find($value)?->hasRole('instructor')) {
                        $fail('User yang dipilih bukan instruktur.');
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul kursus wajib diisi.',
            'description.required' => 'Deskripsi kursus wajib diisi.',
            'price.required' => 'Harga kursus wajib diisi.',
            'level.required' => 'Level kursus wajib dipilih.',
            'status.required' => 'Status kursus wajib dipilih.',
            'instructor_id.required' => 'Instruktur wajib dipilih.',
        ];
    }
}