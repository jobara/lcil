<?php

namespace App\Models;

use App\Enums\LawPolicyTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class LawPolicySource extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

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
