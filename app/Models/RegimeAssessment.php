<?php

namespace App\Models;

use App\Enums\RegimeAssessmentStatuses;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class RegimeAssessment extends Model implements Auditable
{
    use HasFactory;
    use HasSlug;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'jurisdiction',
        'municipality',
        'description',
        'year_in_effect',
        'status,',
    ];

    /**
     * The attributes which should be cast to other types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => RegimeAssessmentStatuses::class,
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['evaluations'];

    public function lawPolicySources(): BelongsToMany
    {
        return $this->belongsToMany(LawPolicySource::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function (self $model): string {
                $date = Carbon::now()->format('Ymd');

                return "RA-{$date}";
            })
            ->saveSlugsTo('ra_id');
    }

    public function getRouteKeyName(): string
    {
        return 'ra_id';
    }
}
