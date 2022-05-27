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

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['jurisdiction'] ?? false, fn ($query, $jurisdiction) =>
            $query->where(fn($query) =>
                $query->where('jurisdiction', $jurisdiction)
                      ->orWhere('jurisdiction', 'like', "{$jurisdiction}-%")
            )
        );

        $query->when(
            $filters['keywords'] ?? false,
            // uses a boolean full text search
            // see: https://dev.mysql.com/doc/refman/8.0/en/fulltext-boolean.html
            // If we use the natural language full text search, words that appear in more than 50% of the records are
            // treated as stopwords and will not return results.
            // see: https://dev.mysql.com/doc/refman/8.0/en/fulltext-natural-language.html
            fn ($query, $keywords) => $query->whereFullText(['name'], $keywords, ['mode' => 'boolean'])
        );
    }
}
