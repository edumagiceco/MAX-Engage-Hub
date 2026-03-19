<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseLibrary extends Model
{
    use HasFactory;

    protected $table = 'case_library';

    protected $fillable = [
        'title',
        'industry',
        'department',
        'problem',
        'solution_type',
        'summary',
        'outcome',
        'metrics_json',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'metrics_json' => 'array',
        ];
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
}
