<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPhoto extends Model
{
    protected $fillable = [
        'period_id',
        'photo_path',
        'caption',
        'sort_order',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(OathPeriod::class, 'period_id');
    }
}
