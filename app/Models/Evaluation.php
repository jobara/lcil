<?php

namespace App\Models;

use App\Enums\EvaluationAssessments;
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
}
