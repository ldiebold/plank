<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['challenge_id', 'user_id', 'date', 'duration_seconds'])]
class PlankCompletion extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $plankCompletion): void {
            if (blank($plankCompletion->completed_at)) {
                $plankCompletion->completed_at = now();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'duration_seconds' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
