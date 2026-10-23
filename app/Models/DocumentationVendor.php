<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentationVendor extends Model
{
    protected $fillable = [
        'study_program_id',
        'category',
        'name',
        'contact_person',
        'whatsapp_number',
        'instagram_handle',
        'youtube_channel',
        'price_estimate',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePhotographers(Builder $query): Builder
    {
        return $query->where('category', 'photographer');
    }

    public function scopeLiveStreaming(Builder $query): Builder
    {
        return $query->where('category', 'live_streaming');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'live_streaming' => 'Streaming Live Youtube',
            default => 'Fotografer Ruangan',
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'live_streaming' => 'fa-brands fa-youtube',
            default => 'fa-solid fa-camera-retro',
        };
    }

    public function getWhatsappUrlAttribute(): string
    {
        $raw = preg_replace('/[^0-9]/', '', (string) $this->whatsapp_number);
        if (str_starts_with($raw, '0')) {
            $raw = '62'.substr($raw, 1);
        } elseif (! str_starts_with($raw, '62') && ! empty($raw)) {
            $raw = '62'.$raw;
        }

        $service = $this->category === 'live_streaming' ? 'jasa Streaming Live Youtube' : 'layanan Fotografer dokumentasi';
        $message = "Halo {$this->name}, saya calon wisudawan Sumpah Profesi FK UNTAN, ingin tanya info & konsultasi {$service}.";

        return 'https://wa.me/'.$raw.'?text='.rawurlencode($message);
    }
}
