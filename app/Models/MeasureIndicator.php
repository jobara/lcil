<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasureIndicator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'description'
    ];

    public function dimension()
    {
        return $this->belongsTo(MeasureDimension::class, 'measure_dimension_id');
    }

    public function measures()
    {
        return $this->hasMany(Measure::class, 'measure_indicator_id');
    }
}
