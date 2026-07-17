<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venture extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'sector',
        'stage',
        'goal_amount',
        'raised_amount',
        'status',
        'logo_path',
        'pitch_deck_path',
        'views',
    ];

    protected $casts = [
        'goal_amount'   => 'decimal:2',
        'raised_amount' => 'decimal:2',
    ];

    /** The founder who owns this venture */
    public function founder()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Campaigns under this venture */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    /** Investor interests for this venture */
    public function interests()
    {
        return $this->hasMany(InvestorInterest::class);
    }

    /** Funding progress percentage */
    public function progressPercent(): float
    {
        if ($this->goal_amount <= 0) return 0;
        return min(100, round(($this->raised_amount / $this->goal_amount) * 100, 1));
    }
}
