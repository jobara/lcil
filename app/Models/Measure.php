<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Measure extends Model
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
        'title',
        'type',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(MeasureIndicator::class, 'measure_indicator_id');
    }
}
