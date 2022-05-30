<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
 * @property-read \Illuminate\Database\Eloquent\Collection/\App\Models\Measure[] $measures
 */

class MeasureIndicator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'description',
    ];

    /**
     * Get the Measure Dimension that this Measure Indicator belongs to.
     *
     * @return BelongsTo
     */
    public function dimension(): BelongsTo
    {
        return $this->belongsTo(MeasureDimension::class, 'measure_dimension_id');
    }

    /**
     *  Get the Measures that belong to this Measure Indicator
     *
     * @return HasMany
     */
    public function measures(): HasMany
    {
        return $this->hasMany(Measure::class, 'measure_indicator_id');
    }
}
