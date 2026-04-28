<?php
// app/Models/UserSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $table = 'user_settings';

    protected $fillable = [
        'user_id',
        'language',
        'timezone',
        'date_format',
        'dark_mode',
        'compact_view',
        'email_notifications',
        'push_notifications',
        'assignment_reminder',
        'quiz_reminder',
        'course_update_notification',
        'forum_reply_notification',
        'certificate_notification',
        'event_reminder',
        'auto_play_video',
        'show_subtitles',
        'video_quality',
        'auto_mark_complete',
        'daily_goal_minutes',
        'profile_public',
        'show_progress',
        'show_certificates',
        'allow_messages',
        'high_contrast',
        'large_text',
        'screen_reader'
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
        'compact_view' => 'boolean',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'assignment_reminder' => 'boolean',
        'quiz_reminder' => 'boolean',
        'course_update_notification' => 'boolean',
        'forum_reply_notification' => 'boolean',
        'certificate_notification' => 'boolean',
        'event_reminder' => 'boolean',
        'auto_play_video' => 'boolean',
        'show_subtitles' => 'boolean',
        'auto_mark_complete' => 'boolean',
        'profile_public' => 'boolean',
        'show_progress' => 'boolean',
        'show_certificates' => 'boolean',
        'allow_messages' => 'boolean',
        'high_contrast' => 'boolean',
        'large_text' => 'boolean',
        'screen_reader' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getForUser($userId)
    {
        return self::firstOrCreate(['user_id' => $userId]);
    }
}