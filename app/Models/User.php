<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'default_role',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'last_seen_at',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'deleted_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    protected $appends = ['role_name', 'avatar_url', 'is_email_verified'];

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

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withTimestamps()
            ->withPivot('earned_at');
    }

    /**
     * Notifikasi in-app (tabel notifications kustom). Diberi nama berbeda dari
     * notifications() milik trait Notifiable karena skema tabelnya berbeda
     * (user_id, bukan notifiable_type/notifiable_id) dan tidak kompatibel.
     */
    public function appNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
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

    // Accessor untuk role name
    public function getRoleNameAttribute()
    {
        return $this->roles->first()->name ?? 'student';
    }

    // Accessor untuk avatar URL
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=769826&color=white';
    }

    // Accessor untuk email verified status
    public function getIsEmailVerifiedAttribute()
    {
        return !is_null($this->email_verified_at);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
    }

    /**
     * Get all tokens for the user
     */
    public function tokens()
    {
        return $this->hasMany(PersonalAccessToken::class, 'tokenable_id');
    }

    /**
     * Create a new token for API access
     */
    public function createApiToken(string $name, array $abilities = ['*'], ?int $expiresInDays = null): string
    {
        $expiresAt = $expiresInDays ? now()->addDays($expiresInDays) : null;

        $token = $this->createToken($name, $abilities, $expiresAt);

        // Log token creation
        ActivityLog::create([
            'user_id' => $this->id,
            'action' => 'create_api_token',
            'description' => "Created API token: {$name}",
            'ip_address' => request()->ip(),
        ]);

        return $token->plainTextToken;
    }

    /**
     * Revoke all tokens for a user
     */
    public function revokeAllTokens(): void
    {
        $count = $this->tokens()->count();
        $this->tokens()->delete();

        ActivityLog::create([
            'user_id' => $this->id,
            'action' => 'revoke_all_tokens',
            'description' => "Revoked {$count} API tokens",
            'ip_address' => request()->ip(),
        ]);
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