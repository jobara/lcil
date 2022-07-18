<?php

namespace App\Models;

use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Provision extends Model implements Auditable
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
        'section',
        'body',
        'reference',
        'legal_capacity_approach',
        'decision_making_capability',
        'decision_type',
        'court_challenge',
        'decision_citation',
    ];

    /**
     * The attributes which should be cast to other types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'decision_type' => 'json',
        'decision_making_capability' => 'json',
        'court_challenge' => ProvisionCourtChallenges::class,
        'legal_capacity_approach' => LegalCapacityApproaches::class,
    ];

    public function lawPolicySource(): BelongsTo
    {
        return $this->belongsTo(LawPolicySource::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['section'])
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }
}
