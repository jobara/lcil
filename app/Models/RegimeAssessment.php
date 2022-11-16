<?php

namespace App\Models;

use App\Enums\RegimeAssessmentStatuses;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        'year_of_assessment',
        'status',
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

    /**
     * Filter Regime Assessments based on jurisdiction and keywords.
     *
     * @param  Builder  $query
     * @param  array{jurisdiction: ?string, keywords: ?string, status: ?string}  $filters The jurisdiction and keywords
     * to filter the search results with.
     * @return void
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when(
            $filters['jurisdiction'] ?? false,
            fn ($query, $jurisdiction) => $query->where(
                fn ($query) => $query->where('jurisdiction', $jurisdiction)
                    ->orWhere('jurisdiction', 'like', "{$jurisdiction}-%")
            )
        );

        $query->when(
            $filters['keywords'] ?? false,
            // uses a boolean full text search
            // see: https://dev.mysql.com/doc/refman/8.0/en/fulltext-boolean.html
            // If we use the natural language full text search, words that appear in more than 50% of the records are
            // treated as stopwords and will not return results.
            // see: https://dev.mysql.com/doc/refman/8.0/en/fulltext-natural-language.html
            fn ($query, $keywords) => $query->whereFullText(['description'], $keywords, ['mode' => 'boolean'])
        );

        $query->when(
            $filters['status'] ?? false,
            fn ($query, $status) => $query->where('status', $status)
        );
    }
}
