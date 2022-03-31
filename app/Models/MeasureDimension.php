<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasureDimension extends Model
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

    public function indicators()
    {
        return $this->hasMany(MeasureIndicator::class, 'measure_dimension_id');
    }
}
