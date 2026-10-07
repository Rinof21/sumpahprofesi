<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyProgram extends Model
{
    protected $fillable = [
        'code',
        'name',
        'degree_title',
        'organization',
        'slug',
        'oath_pdf_path',
    ];

    public function oathPeriods(): HasMany
    {
        return $this->hasMany(OathPeriod::class);
    }

    public function contactPeople(): HasMany
    {
        return $this->hasMany(ContactPerson::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
