<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'description',
    'created_by',
    'starting_time_seconds',
    'daily_increment_seconds',
    'goal_time_seconds',
    'invite_code',
    'start_date',
    'is_active',
])]
#[Hidden([])]
class Challenge extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Challenge $challenge): void {
            if ($challenge->invite_code) {
                return;
            }

            do {
                $inviteCode = Str::random(12);
            } while (static::where('invite_code', $inviteCode)->exists());

            $challenge->invite_code = $inviteCode;
        });
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'is_active' => 'boolean',
            'starting_time_seconds' => 'integer',
            'daily_increment_seconds' => 'integer',
            'goal_time_seconds' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'challenge_user')
            ->withPivot('joined_at')
            ->withTimestamps('joined_at', 'updated_at');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(PlankCompletion::class);
    }

    public function getTodayTargetAttribute(): int
    {
        $startDate = $this->start_date;
        $startingTime = $this->starting_time_seconds ?? 0;
        $dailyIncrement = $this->daily_increment_seconds ?? 0;
        $goalTime = $this->goal_time_seconds ?? $startingTime;

        if (! $startDate) {
            return min($startingTime, $goalTime);
        }

        $daysSinceStart = max(0, $startDate->startOfDay()->diffInDays(now()->startOfDay(), false));
        $todayTarget = $startingTime + ($daysSinceStart * $dailyIncrement);

        return min($todayTarget, $goalTime);
    }
}
