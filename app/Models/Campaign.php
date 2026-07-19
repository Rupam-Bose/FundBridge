<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Investment;

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

    /** Investments made into this campaign */
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    /** Funding progress percentage */
    public function progressPercent(): float
    {
        $goal   = floatval($this->goal);
        $raised = floatval($this->raised);
        if ($goal <= 0) return 0;
        return min(100, round(($raised / $goal) * 100, 1));
    }
}
