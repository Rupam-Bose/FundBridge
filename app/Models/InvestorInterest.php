<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorInterest extends Model
{
    protected $fillable = [
        'investor_id',
        'venture_id',
        'interest_level',
        'note',
    ];

    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    public function venture()
    {
        return $this->belongsTo(Venture::class);
    }
}
