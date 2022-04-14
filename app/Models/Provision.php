<?php

namespace App\Models;

use App\Enums\LegalChallengeTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Provision extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'section',
        'body',
        'reference',
        'type_of_decision',
        'is_subject_to_challenge',
        'is_result_of_challenge',
        'decision_citation',
    ];

    protected $casts = [
        'type' => LegalChallengeTypeEnum::class
    ];

    public function lawPolicySource(): BelongsTo
    {
        return $this->belongsTo(LawPolicySource::class);
    }
}
