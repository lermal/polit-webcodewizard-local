<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login' => 'datetime',
    ];

    /**
     * Группы, в которых состоит пользователь
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->withPivot('role') // роль пользователя в группе
            ->withTimestamps();
    }

    /**
     * Группы, где пользователь является учителем
     */
    public function teacherGroups()
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->wherePivot('role', 'teacher')
            ->withTimestamps();
    }

    /**
     * Каналы, к которым у пользователя есть доступ
     */
    public function channels()
    {
        return $this->belongsToMany(Channel::class, 'channel_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Сообщения пользователя
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Проверка роли пользователя в системе
     */
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    /**
     * Проверка роли пользователя в конкретной группе
     */
    public function hasGroupRole(Group $group, $role): bool
    {
        return $this->groups()
            ->wherePivot('group_id', $group->id)
            ->wherePivot('role', $role)
            ->exists();
    }

    /**
     * Проверка роли пользователя в канале
     */
    public function hasChannelRole(Channel $channel, $role): bool
    {
        return $this->channels()
            ->wherePivot('channel_id', $channel->id)
            ->wherePivot('role', $role)
            ->exists();
    }

    /**
     * Проверка является ли пользователь администратором
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Проверка является ли пользователь учителем
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Проверка активен ли пользователь
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Получить все непрочитанные сообщения пользователя
     */
    public function unreadMessages()
    {
        return $this->belongsToMany(Message::class, 'message_user')
            ->wherePivot('read_at', null);
    }

    /**
     * Получить все группы с непрочитанными сообщениями
     */
    public function groupsWithUnreadMessages()
    {
        return $this->groups()
            ->whereHas('channels.messages', function ($query) {
                $query->whereDoesntHave('readBy', function ($q) {
                    $q->where('user_id', $this->id);
                });
            });
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    public function testRatings()
    {
        return $this->hasMany(TestRating::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class, 'created_by');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Europe/Moscow');
    }

    public function getLastLoginAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Europe/Moscow') : null;
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function sendEmailVerificationNotification()
    {
        Mail::to($this->email)->send(new VerifyEmail($this));
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->send(new ResetPassword($this, $token));
    }
}
