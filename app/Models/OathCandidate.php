<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OathCandidate extends Model
{
    protected $fillable = [
        'period_id',
        'user_id',
        'nim',
        'nik',
        'full_name',
        'birth_place',
        'birth_date',
        'father_name',
        'mother_name',
        'admission_path',
        'religion',
        'agreed_rules',
        'agreed_at',
        'ppt_file_path',
        'is_speech_rep',
        'speech_notes',
        'is_oath_coordinator',
        'coordinator_notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'agreed_rules' => 'boolean',
        'agreed_at' => 'datetime',
        'is_speech_rep' => 'boolean',
        'is_oath_coordinator' => 'boolean',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(OathPeriod::class, 'period_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
