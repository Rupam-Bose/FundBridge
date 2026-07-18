<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $fillable = [
        'investor_id',
        'venture_id',
        'campaign_id',
        'amount',
        'note',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    public function venture()
    {
        return $this->belongsTo(Venture::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
