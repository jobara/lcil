<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasureDimension extends Model
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
     * Get the Measure Indicators that belong to this Measure Dimension
     *
     * @return HasMany
     */
    public function indicators(): HasMany
    {
        return $this->hasMany(MeasureIndicator::class, 'measure_dimension_id');
    }
}
