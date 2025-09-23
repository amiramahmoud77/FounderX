<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'verification_code',
        'verification_code_sent_at',
        'password_reset_code',
        'password_reset_code_sent_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];
    protected $attributes = [
        'role' => 'founder',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_code_sent_at' => 'datetime',
        'password_reset_code_sent_at' => 'datetime',
        'password' => 'hashed',
    ];

    // في ملف User.php
// إضافة هذه الدالة للتحقق من وجود كلمة مرور
public function hasPassword(): bool
{
    return !empty($this->password);
}


// استبدل الدالة الحالية بهذه
public function needsPassword(): bool
{
    return empty($this->password) && !$this->hasVerifiedEmail();
}

    public function sendPasswordResetCode(): void
{
    $resetCode = (string) mt_rand(1000, 9999); // 4-digit code

    $this->password_reset_code = $resetCode;
    $this->password_reset_code_sent_at = now();
    $this->save();

    // إرسال الإيميل بكود الاستعادة
    $this->notify(new \App\Notifications\PasswordResetCodeNotification($resetCode));
}

    public function generateVerificationCode(): string
    {
        return (string) mt_rand(1000, 9999); // Changed to 4 digits
    }

    public function sendVerificationCode(): void
    {
        $this->verification_code = $this->generateVerificationCode();
        $this->verification_code_sent_at = now();
        $this->save();

        // Send email here
        $this->notify(new \App\Notifications\VerifyEmailCode($this->verification_code));
    }

    public function pitches(): HasMany
    {
        return $this->hasMany(Pitch::class);
    }

    public function pitchesText(): HasMany
    {
        return $this->hasMany(PitchText::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function feedbacks(): HasManyThrough
    {
        return $this->hasManyThrough(Feedback::class, Pitch::class);
    }

    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    public function isFounder(): bool
    {
        return $this->hasRole('founder');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isInvestor(): bool
    {
        return $this->hasRole('investor');
    }

    public function scopeFounder($query)
    {
        return $query->where('role','founder');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role','admin');
    }

    public function scopeInvestor($query)
    {
        return $query->where('role','investor');
    }
}
