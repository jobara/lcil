<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'description',
        'title',
        'type'
    ];

    public function indicator()
    {
        return $this->belongsTo(MeasureIndicator::class, 'measure_indicator_id');
    }
}
