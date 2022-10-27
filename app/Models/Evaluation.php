<?php

namespace App\Models;

use App\Enums\EvaluationAssessments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'assessment',
        'comment',
    ];

    /**
     * The attributes which should be cast to other types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assessment' => EvaluationAssessments::class,
    ];

    public function regimeAssessment(): BelongsTo
    {
        return $this->belongsTo(RegimeAssessment::class, 'regime_assessment_id');
    }

    public function measure(): BelongsTo
    {
        return $this->belongsTo(Measure::class);
    }

    public function provision(): BelongsTo
    {
        return $this->belongsTo(Provision::class);
    }

    /**
     * Filter Evaluations based on regimeAssessment, measure, provision, assessment.
     *
     * @param  Builder  $query
     * @param  array{ra_id: ?string, measureCode: ?string, provisionID: ?string, assessment: ?string}  $filters
     * @return void
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when(
            $filters['ra_id'] ?? false,
            fn ($query, $ra_id) => $query->where('regime_assessment_id', RegimeAssessment::firstWhere('ra_id', $ra_id)?->id),
        );

        $query->when(
            $filters['measureCode'] ?? false,
            fn ($query, $code) => $query->where('measure_id', Measure::firstWhere('code', $code)?->id),
        );

        $query->when(
            $filters['provisionID'] ?? false,
            fn ($query, $id) => $query->where('provision_id', $id),
        );

        $query->when(
            $filters['assessment'] ?? false,
            fn ($query, $assessment) => $query->where('assessment', $assessment)
        );
    }
}
