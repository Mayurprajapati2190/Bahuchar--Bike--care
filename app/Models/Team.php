<?php

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'slug',
    'address',
    'phone',
    'hours',
    'tagline',
    'gstin',
    'bill_prefix',
])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Team $team): void {
            if (blank($team->slug)) {
                $team->slug = static::uniqueSlug($team->name);
            }

            if (blank($team->bill_prefix)) {
                $team->bill_prefix = config('shop.bill_prefix', 'BBC');
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'shop';
        $slug = $base;
        $i = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public static function ensureDefault(): self
    {
        $existing = static::query()->orderBy('id')->first();

        if ($existing) {
            return $existing;
        }

        return static::query()->create([
            'name' => config('shop.name') ?: 'Bahuchar Bike Care',
            'address' => config('shop.address'),
            'phone' => config('shop.phone'),
            'hours' => config('shop.hours'),
            'tagline' => config('shop.tagline'),
            'gstin' => config('shop.gstin'),
            'bill_prefix' => config('shop.bill_prefix', 'BBC'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function shopPayload(): array
    {
        return [
            'name' => $this->name ?: config('shop.name'),
            'tagline' => $this->tagline ?: config('shop.tagline'),
            'address' => $this->address ?: config('shop.address'),
            'phone' => $this->phone ?: config('shop.phone'),
            'hours' => $this->hours ?: config('shop.hours'),
            'gstin' => $this->gstin ?: config('shop.gstin'),
            'bill_prefix' => $this->bill_prefix ?: config('shop.bill_prefix', 'BBC'),
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
