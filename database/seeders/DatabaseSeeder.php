<?php

namespace Database\Seeders;

use App\Models\CaseLibrary;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => env('SEED_ADMIN_EMAIL', 'admin@example.com'),
        ], [
            'name' => env('SEED_ADMIN_NAME', 'MAX Admin'),
            'password' => env('SEED_ADMIN_PASSWORD', 'ChangeMe1234!'),
            'role' => 'admin',
        ]);

        if (app()->environment(['local', 'testing'])) {
            foreach ($this->sampleCases() as $case) {
                CaseLibrary::query()->firstOrCreate([
                    'title' => $case['title'],
                ], $case);
            }
        }
    }

    private function sampleCases(): array
    {
        return [
            [
                'title' => '문서 검색 정확도 개선을 위한 RAG 파일럿',
                'industry' => '교육',
                'department' => '운영',
                'problem' => '내부 문서가 흩어져 있어 답변 일관성이 낮음',
                'solution_type' => 'rag',
                'summary' => '운영 문서와 FAQ를 기반으로 RAG 검색형 챗봇 파일럿을 진행했다.',
                'outcome' => '상담 준비 시간이 줄고 반복 문의 응답 품질이 안정화되었다.',
                'metrics_json' => [
                    ['label' => '답변 준비 시간', 'value' => '65% 단축'],
                    ['label' => '반복 응답 정확도', 'value' => '92%'],
                ],
                'status' => 'approved',
            ],
            [
                'title' => '반복 보고 업무 자동화 워크플로우 구축',
                'industry' => '제조',
                'department' => '생산관리',
                'problem' => '주간 보고 자료 취합과 정리 시간이 과도하게 소요됨',
                'solution_type' => 'workflow_automation',
                'summary' => '반복 엑셀 취합과 승인 루프를 자동화해 업무 흐름을 재구성했다.',
                'outcome' => '수작업 비중이 낮아지고 보고 리드타임이 짧아졌다.',
                'metrics_json' => [
                    ['label' => '보고 리드타임', 'value' => '58% 단축'],
                    ['label' => '수작업 단계', 'value' => '7단계 → 3단계'],
                ],
                'status' => 'approved',
            ],
            [
                'title' => '현업 실무자 대상 AX 기초 교육 설계',
                'industry' => '서비스',
                'department' => '전략',
                'problem' => '도구는 검토 중이지만 내부 활용 역량이 부족함',
                'solution_type' => 'education',
                'summary' => '현업 중심으로 기초 교육과 적용 과제 워크숍을 함께 운영했다.',
                'outcome' => '교육 이후 파일럿 과제가 발굴되고 후속 제안으로 이어졌다.',
                'metrics_json' => [
                    ['label' => '실습 과제 도출', 'value' => '12건'],
                    ['label' => '후속 파일럿 전환', 'value' => '3건'],
                ],
                'status' => 'approved',
            ],
        ];
    }
}
