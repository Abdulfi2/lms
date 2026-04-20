<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;
    
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'default_role',
        'is_active', 'last_login_at', 'last_login_ip',
        'two_factor_enabled', 'two_factor_secret', 'two_factor_recovery_codes',
    ];
    
    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'deleted_at' => 'datetime',
    ];
    
    // ========== RELATIONSHIPS ==========
    
    public function profile()
    {
        return $this->morphOne(Profile::class, 'profileable');
    }
    
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }
    
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withPivot('progress', 'status', 'enrolled_at', 'completed_at')
                    ->withTimestamps();
    }
    
    public function assignments()
    {
        return $this->hasMany(Submission::class);
    }
    
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
    
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    // ========== HELPER METHODS ==========
    
    public function isStudent(): bool
    {
        return $this->hasRole('student') || $this->default_role === 'student';
    }
    
    public function isInstructor(): bool
    {
        return $this->hasRole('instructor') || $this->default_role === 'instructor';
    }
    
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->default_role === 'admin';
    }
    
    // ========== SCOPES ==========
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeByRole($query, $role)
    {
        return $query->role($role);
    }
}