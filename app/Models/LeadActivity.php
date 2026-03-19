<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeadActivity extends Model
{
    use HasFactory;

    public const SUBMISSION_ACTIVITY_TYPES = [
        'contact_submitted',
        'education_inquiry_submitted',
        'diagnosis_completed',
        'recommendation_requested',
    ];

    protected $fillable = [
        'customer_id',
        'activity_type',
        'source',
        'title',
        'payload_json',
    ];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isSubmissionActivity(): bool
    {
        return in_array($this->activity_type, self::SUBMISSION_ACTIVITY_TYPES, true);
    }

    public function formLabel(): ?string
    {
        return match ($this->payload_json['form_type'] ?? null) {
            'contact' => '일반 문의',
            'education' => '교육 문의',
            'diagnosis' => 'AX 진단',
            'recommendation' => 'AX 맞춤 추천',
            default => null,
        };
    }

    public function summaryLine(): ?string
    {
        $payload = $this->payload_json ?? [];

        foreach (['message', 'details'] as $key) {
            $value = trim((string) ($payload[$key] ?? ''));

            if ($value !== '') {
                return Str::limit(preg_replace('/\s+/u', ' ', $value) ?: $value, 120);
            }
        }

        $parts = [];

        foreach (['interest_area', 'current_stage', 'team_size', 'status', 'tag', 'overall_score', 'recommendation_type', 'due_date'] as $key) {
            $value = $this->formatValue($key, $payload[$key] ?? null);

            if ($value === null) {
                continue;
            }

            $parts[] = match ($key) {
                'interest_area' => "관심 영역: {$value}",
                'current_stage' => "현재 단계: {$value}",
                'team_size' => "팀 규모: {$value}",
                'status' => "상태: {$value}",
                'tag' => "태그: {$value}",
                'overall_score' => "진단 점수: {$value}",
                'recommendation_type' => "추천: {$value}",
                'due_date' => "예정일: {$value}",
                default => $value,
            };

            if (count($parts) === 3) {
                break;
            }
        }

        return $parts === [] ? null : implode(' · ', $parts);
    }

    public function detailItems(): array
    {
        $payload = $this->payload_json ?? [];
        $items = [];

        foreach ($this->detailFieldMap() as $key => $label) {
            if (! array_key_exists($key, $payload)) {
                continue;
            }

            $value = $this->formatValue($key, $payload[$key]);

            if ($value === null) {
                continue;
            }

            $items[] = [
                'label' => $label,
                'value' => $value,
                'multiline' => in_array($key, ['message', 'details'], true),
            ];
        }

        return $items;
    }

    private function detailFieldMap(): array
    {
        return [
            'form_type' => '제출 폼',
            'submitted_name' => '이름',
            'submitted_email' => '이메일',
            'submitted_phone' => '연락처',
            'submitted_company_name' => '회사명',
            'submitted_job_title' => '직책',
            'message' => '문의 내용',
            'details' => '상세 설명',
            'interest_area' => '관심 영역',
            'current_stage' => '현재 단계',
            'team_size' => '팀 규모',
            'consent_marketing' => '마케팅 동의',
            'status' => '변경 상태',
            'tag' => '태그',
            'due_date' => '예정일',
            'overall_score' => '진단 점수',
            'maturity_level' => '성숙도',
            'recommendation_type' => '추천 타입',
        ];
    }

    private function formatValue(string $key, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);

            if ($value === '') {
                return null;
            }
        }

        return match ($key) {
            'form_type' => $this->formLabel(),
            'interest_area', 'recommendation_type' => match ((string) $value) {
                'education' => '교육',
                'consulting' => '컨설팅',
                'rag' => 'RAG',
                'ocr' => 'OCR',
                'workflow_automation' => '업무 자동화',
                default => Str::headline(str_replace('_', ' ', (string) $value)),
            },
            'current_stage' => match ((string) $value) {
                'none' => '도입 전',
                'exploring' => '검토 중',
                'pilot' => '파일럿 진행',
                'rollout' => '부분 운영',
                'companywide' => '전사 확산',
                default => Str::headline(str_replace('_', ' ', (string) $value)),
            },
            'maturity_level' => match ((string) $value) {
                'starter' => 'Starter',
                'emerging' => 'Emerging',
                'scaling' => 'Scaling',
                'advanced' => 'Advanced',
                default => Str::headline(str_replace('_', ' ', (string) $value)),
            },
            'consent_marketing' => (bool) $value ? '동의' : '미동의',
            'overall_score' => is_numeric($value) ? "{$value}점" : (string) $value,
            default => is_array($value) ? implode(', ', array_map(static fn ($item): string => (string) $item, $value)) : (string) $value,
        };
    }
}
