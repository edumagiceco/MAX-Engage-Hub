<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory;

    public const STATUS_OPTIONS = [
        'new',
        'in_review',
        'contacted',
        'meeting_scheduled',
        'proposal',
        'won',
        'lost',
        'nurturing',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'job_title',
        'consent_marketing',
        'latest_source',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'consent_marketing' => 'boolean',
        ];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(LeadActivity::class)->latestOfMany();
    }

    public function tags(): HasMany
    {
        return $this->hasMany(CustomerTag::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class)->latest();
    }

    public function followUpTasks(): HasMany
    {
        return $this->hasMany(FollowUpTask::class)->latest('due_date');
    }
}
