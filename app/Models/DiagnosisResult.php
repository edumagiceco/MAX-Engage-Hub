<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosisResult extends Model
{
    use HasFactory;

    public const MATURITY_LEVELS = [
        'starter',
        'emerging',
        'scaling',
        'advanced',
    ];

    protected $fillable = [
        'customer_id',
        'overall_score',
        'maturity_level',
        'main_pain_points',
        'summary',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
