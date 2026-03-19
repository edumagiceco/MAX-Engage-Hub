<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationResult extends Model
{
    use HasFactory;

    public const TYPE_OPTIONS = [
        'education',
        'consulting',
        'rag',
        'ocr',
        'workflow_automation',
    ];

    protected $fillable = [
        'customer_id',
        'recommendation_type',
        'recommendation_reason',
        'next_action',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
