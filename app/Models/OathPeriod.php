<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OathPeriod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'study_program_id',
        'name',
        'slug',
        'event_date',
        'quarter_code',
        'access_token',
        'drive_url',
        'youtube_url',
        'status',
        'is_locked',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_locked' => 'boolean',
    ];

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(OathCandidate::class, 'period_id')->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function photographers(): HasMany
    {
        return $this->hasMany(Photographer::class, 'period_id');
    }

    public function eventPhotos(): HasMany
    {
        return $this->hasMany(EventPhoto::class, 'period_id')->orderBy('sort_order', 'asc');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(EventSchedule::class, 'period_id')->orderBy('sort_order', 'asc')->orderBy('start_time', 'asc');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(EventRule::class, 'period_id')->orderBy('sort_order', 'asc');
    }
}
