<?php

namespace App\Models;

use App\Enums\LawPolicyTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LawPolicySource extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'type',
        'is_core',
        'reference',
        'jurisdiction',
        'municipality',
        'year_in_effect',
    ];

    protected $casts = [
        'type' => LawPolicyTypeEnum::class
    ];

    public function provisions(): HasMany
    {
        return $this->hasMany(Provision::class);
    }
}
