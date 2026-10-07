<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photographer extends Model
{
    protected $fillable = [
        'period_id',
        'name',
        'agency_name',
        'phone_number',
        'badge_code',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(OathPeriod::class, 'period_id');
    }
}
