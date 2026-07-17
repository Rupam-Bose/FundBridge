<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'venture_id',
        'title',
        'description',
        'goal',
        'raised',
        'deadline',
        'status',
    ];

    protected $casts = [
        'goal'     => 'decimal:2',
        'raised'   => 'decimal:2',
        'deadline' => 'date',
    ];

    /** The venture this campaign belongs to */
    public function venture()
    {
        return $this->belongsTo(Venture::class);
    }

    /** Funding progress percentage */
    public function progressPercent(): float
    {
        if ($this->goal <= 0) return 0;
        return min(100, round(($this->raised / $this->goal) * 100, 1));
    }
}
