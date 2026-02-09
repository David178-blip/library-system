<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'message', 'is_read'];

    // Flag to disable auto-email sending (used when sending custom emails)
    public static $disableAutoEmail = false;

    protected static function booted(): void
    {
        static::created(function (Notification $notification) {
            // Skip auto-email if disabled (e.g., when sending custom reminder emails)
            if (static::$disableAutoEmail) {
                return;
            }

            $user = $notification->user;

            if ($user && $user->email) {
                Mail::to($user->email)->send(new GenericNotificationMail($notification));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
