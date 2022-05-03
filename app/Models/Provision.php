<?php

namespace App\Models;

use App\Enums\ApproachToLegalCapacityEnum;
use App\Enums\DecisionMakingCapabilityEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Provision extends Model implements Auditable
{
    use HasFactory;
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
        'is_subject_to_challenge',
        'is_result_of_challenge',
        'decision_citation',
    ];

    protected $casts = [
        'decision_type' => 'json',
        'legal_capacity_approach' => ApproachToLegalCapacityEnum::class,
        'decision_making_capability' => DecisionMakingCapabilityEnum::class
    ];

    public function lawPolicySource(): BelongsTo
    {
        return $this->belongsTo(LawPolicySource::class);
    }
}
