<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = "user";

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_name',
        'avatar',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    Relationships
    */

    /** Ventures this founder owns */
    public function ventures()
    {
        return $this->hasMany(Venture::class, 'user_id');
    }

    /** Messages sent by this user */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /** Messages received by this user */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /** Investor interests this user has marked (investor role) */
    public function interests()
    {
        return $this->hasMany(InvestorInterest::class, 'investor_id');
    }

    /*
    Helpers
    */

    public function isFounder(): bool
    {
        return $this->role === 'founder';
    }

    public function isInvestor(): bool
    {
        return $this->role === 'investor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function avatarUrl(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=00d99c&color=fff&size=100';
    }

    /** Unread messages count */
    public function unreadMessagesCount(): int
    {
        return $this->receivedMessages()->whereNull('read_at')->count();
    }
}
