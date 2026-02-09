<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail {
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','role', 'course',
    ];

    protected $hidden = [
        'password','remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verification_expires_at' => 'datetime',
    ];

    public function borrows() {
        return $this->hasMany(Borrow::class);
    }

    /**
     * Admins are not required to verify email.
     */
    public function hasVerifiedEmail(): bool
    {
        if ($this->role === 'admin') {
            return true;
        }
        return $this->email_verified_at !== null;
    }
}
