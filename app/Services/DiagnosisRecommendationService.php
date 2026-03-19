<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\RecommendationResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DiagnosisRecommendationService
{
    public function process(string $form, Customer $customer, array $attributes): void
    {
        $interest = $this->normalizeInterestArea($attributes['interest_area'] ?? null);

        if ($interest) {
            $this->attachTags($customer, $this->interestTags($interest));
        }

        if ($form === 'diagnosis') {
            $diagnosis = $customer->diagnosisResults()->create(
                $this->buildDiagnosisPayload($attributes)
            );

            $recommendation = $customer->recommendationResults()->create(
                $this->buildRecommendationPayload($attributes, $diagnosis->maturity_level, $diagnosis->overall_score)
            );

            $this->attachTags(
                $customer,
                $this->recommendationTags($recommendation->recommendation_type, $diagnosis->overall_score)
            );

            $customer->activities()->create([
                'activity_type' => 'diagnosis_summary_created',
                'source' => 'system',
                'title' => '진단 요약과 1차 추천이 생성되었습니다.',
                'payload_json' => [
                    'overall_score' => $diagnosis->overall_score,
                    'maturity_level' => $diagnosis->maturity_level,
                    'recommendation_type' => $recommendation->recommendation_type,
                ],
            ]);

            return;
        }

        if ($form === 'recommendation') {
            $recommendation = $customer->recommendationResults()->create(
                $this->buildRecommendationPayload($attributes)
            );

            $this->attachTags(
                $customer,
                $this->recommendationTags($recommendation->recommendation_type, $this->estimateScore($attributes))
            );

            $customer->activities()->create([
                'activity_type' => 'recommendation_summary_created',
                'source' => 'system',
                'title' => '맞춤 추천 요약이 생성되었습니다.',
                'payload_json' => [
                    'recommendation_type' => $recommendation->recommendation_type,
                ],
            ]);
        }
    }

    public function buildDiagnosisPayload(array $attributes): array
    {
        $score = $this->estimateScore($attributes);
        $maturity = $this->maturityLevel($score);

        return [
            'overall_score' => $score,
            'maturity_level' => $maturity,
            'main_pain_points' => $this->extractPainPoints($attributes),
            'summary' => $this->diagnosisSummary($attributes, $score, $maturity),
        ];
    }

    public function buildRecommendationPayload(
        array $attributes,
        ?string $maturity = null,
        ?int $score = null,
    ): array {
        $score ??= $this->estimateScore($attributes);
        $maturity ??= $this->maturityLevel($score);
        $type = $this->recommendationType($attributes, $score);

        return [
            'recommendation_type' => $type,
            'recommendation_reason' => $this->recommendationReason($attributes, $type, $maturity, $score),
            'next_action' => $this->nextAction($type, $maturity),
        ];
    }

    public function estimateScore(array $attributes): int
    {
        $score = match ($this->normalizeStage($attributes['current_stage'] ?? null)) {
            'exploring', 'none' => 28,
            'pilot' => 52,
            'rollout' => 71,
            'companywide' => 84,
            default => 40,
        };

        $teamSize = (string) ($attributes['team_size'] ?? '');
        preg_match('/\d+/', $teamSize, $matches);
        $headcount = (int) ($matches[0] ?? 0);

        if ($headcount >= 20) {
            $score += 12;
        } elseif ($headcount >= 6) {
            $score += 8;
        } elseif ($headcount >= 1) {
            $score += 4;
        }

        $interest = $this->normalizeInterestArea($attributes['interest_area'] ?? null);

        if (in_array($interest, ['rag', 'ocr', 'workflow_automation'], true)) {
            $score += 6;
        }

        if (Str::length((string) ($attributes['details'] ?? '')) > 120) {
            $score += 4;
        }

        return min(100, max(10, $score));
    }

    public function recommendationType(array $attributes, int $score): string
    {
        $interest = $this->normalizeInterestArea($attributes['interest_area'] ?? null);

        if ($interest && in_array($interest, RecommendationResult::TYPE_OPTIONS, true)) {
            return $interest;
        }

        return match (true) {
            $score < 40 => 'education',
            $score < 65 => 'consulting',
            default => 'workflow_automation',
        };
    }

    private function maturityLevel(int $score): string
    {
        return match (true) {
            $score < 35 => 'starter',
            $score < 55 => 'emerging',
            $score < 75 => 'scaling',
            default => 'advanced',
        };
    }

    private function diagnosisSummary(array $attributes, int $score, string $maturity): string
    {
        $interest = $this->normalizeInterestArea($attributes['interest_area'] ?? null) ?: 'general';
        $stage = $this->normalizeStage($attributes['current_stage'] ?? null) ?: 'unspecified';

        return "현재 응답 기준 AX 준비도는 {$maturity} 단계이며, 진단 점수는 {$score}점입니다. "
            ."주요 관심 영역은 {$interest}이고 현재 단계는 {$stage}로 판단됩니다.";
    }

    private function recommendationReason(array $attributes, string $type, string $maturity, int $score): string
    {
        $interest = $this->normalizeInterestArea($attributes['interest_area'] ?? null);
        $stage = $this->normalizeStage($attributes['current_stage'] ?? null) ?: 'unspecified';

        return collect([
            "현재 성숙도는 {$maturity} 수준으로 분석되었습니다.",
            $interest ? "관심 영역이 {$interest}로 명확합니다." : '관심 영역이 아직 넓게 열려 있습니다.',
            "현재 단계는 {$stage}로 분류되어, {$type} 중심의 첫 제안이 적합합니다.",
            "예상 우선순위는 {$score}점 수준의 후속 대응입니다.",
        ])->implode(' ');
    }

    private function nextAction(string $type, string $maturity): string
    {
        return match ($type) {
            'education' => '기초 교육 제안과 함께 팀별 적용 과제 워크숍 일정을 잡습니다.',
            'consulting' => '현업 인터뷰 기반 진단 미팅을 열고 상세 범위를 정의합니다.',
            'rag' => '문서 자산 현황과 PoC 범위를 정리해 RAG 파일럿 미팅을 제안합니다.',
            'ocr' => '문서 유형과 정확도 요구 수준을 확인하고 OCR 파일럿 범위를 설계합니다.',
            'workflow_automation' => $maturity === 'advanced'
                ? '우선 자동화 후보 프로세스를 확정하고 실행 로드맵을 제안합니다.'
                : '반복 업무 흐름을 정리한 뒤 자동화 후보를 우선순위화합니다.',
            default => '후속 상담 일정을 먼저 잡습니다.',
        };
    }

    private function extractPainPoints(array $attributes): ?string
    {
        $raw = trim(collect([
            $attributes['message'] ?? null,
            $attributes['details'] ?? null,
        ])->filter()->implode(' / '));

        return $raw === '' ? null : Str::limit($raw, 350);
    }

    private function attachTags(Customer $customer, array $tags): void
    {
        collect($tags)
            ->filter()
            ->unique()
            ->each(function (string $tag) use ($customer): void {
                $customer->tags()->firstOrCreate([
                    'tag' => $tag,
                ]);
            });
    }

    private function interestTags(string $interest): array
    {
        return match ($interest) {
            'education' => ['education_interest'],
            'rag' => ['rag_interest'],
            'ocr' => ['ocr_interest'],
            'workflow_automation' => ['automation_interest'],
            'consulting' => ['paid_diagnosis_candidate'],
            default => [],
        };
    }

    private function recommendationTags(string $type, int $score): array
    {
        $tags = $this->interestTags($type);

        if ($score >= 60) {
            $tags[] = 'high_intent';
        }

        if ($type === 'consulting') {
            $tags[] = 'paid_diagnosis_candidate';
        }

        return $tags;
    }

    private function normalizeInterestArea(?string $interest): ?string
    {
        $value = Str::of((string) $interest)->lower()->trim()->replace(' ', '_')->toString();

        return match ($value) {
            'education', 'training', '교육' => 'education',
            'consulting', 'strategy', 'consult', '컨설팅' => 'consulting',
            'rag' => 'rag',
            'ocr' => 'ocr',
            'workflow_automation', 'automation', 'workflow', '업무자동화', '자동화' => 'workflow_automation',
            '' => null,
            default => null,
        };
    }

    private function normalizeStage(?string $stage): ?string
    {
        $value = Str::of((string) $stage)->lower()->trim()->replace(' ', '_')->toString();

        return match ($value) {
            'none', 'not_started', '없음', '미도입' => 'none',
            'exploring', 'reviewing', '검토중', '검토', 'explore' => 'exploring',
            'pilot', 'poc', '파일럿' => 'pilot',
            'rollout', 'team_rollout', '부서확산', '확산' => 'rollout',
            'companywide', 'organizationwide', '전사확산', '전사' => 'companywide',
            '' => null,
            default => null,
        };
    }
}
