<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'personal_info' => 'array',
        'contact_info' => 'array',
        'professional_info' => 'array',
        'academic_info' => 'array',
        'identity_documents' => 'array',
        'addresses' => 'array',
        'employment_info' => 'array',
        'bank_accounts' => 'array',
        'body_measurements' => 'array',
        'social_media' => 'array',
        'emergency_contacts' => 'array',
        'medical_info' => 'array',
        'documents' => 'array',
        'education' => 'array',
        'skills' => 'array',
        'preferences' => 'array',
        'statistics' => 'array',
        'verification' => 'array',
        'metadata' => 'array',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function profileable()
    {
        return $this->morphTo();
    }
}