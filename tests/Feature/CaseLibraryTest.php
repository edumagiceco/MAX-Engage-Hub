<?php

namespace Tests\Feature;

use App\Models\CaseLibrary;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaseLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_filter_case_library_entries(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user)->post(route('admin.cases.store'), [
            'title' => 'RAG 상담 준비 사례',
            'industry' => '교육',
            'department' => '운영',
            'problem' => '자료 검색 시간이 길다',
            'solution_type' => 'rag',
            'summary' => 'RAG 기반 검색형 응답 흐름을 설계했다.',
            'outcome' => '상담 준비 시간이 짧아졌다.',
            'metrics_input' => "준비시간: 40% 단축\n응답정확도: 90%",
            'status' => 'approved',
        ])->assertRedirect(route('admin.cases.index'));

        $this->assertDatabaseHas('case_library', [
            'title' => 'RAG 상담 준비 사례',
            'solution_type' => 'rag',
        ]);

        $this->actingAs($user)
            ->get(route('admin.cases.index', ['solution_type' => 'rag']))
            ->assertOk()
            ->assertSee('RAG 상담 준비 사례');
    }

    public function test_customer_detail_shows_related_cases_from_latest_recommendation(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = Customer::query()->create([
            'name' => 'Han Demo',
            'email' => 'han@example.com',
            'status' => 'new',
        ]);

        $customer->recommendationResults()->create([
            'recommendation_type' => 'rag',
            'recommendation_reason' => 'RAG 도입이 적합합니다.',
            'next_action' => 'PoC 범위 미팅',
        ]);

        $matching = CaseLibrary::query()->create([
            'title' => 'RAG 파일럿 사례',
            'industry' => '서비스',
            'department' => 'CS',
            'problem' => '지식 검색 속도 저하',
            'solution_type' => 'rag',
            'summary' => '검색형 응답 자동화',
            'outcome' => '응답 리드타임 개선',
            'status' => 'approved',
        ]);

        CaseLibrary::query()->create([
            'title' => 'OCR 처리 사례',
            'industry' => '금융',
            'department' => '운영',
            'problem' => '문서 입력 수작업',
            'solution_type' => 'ocr',
            'summary' => 'OCR 자동화',
            'outcome' => '입력 시간 단축',
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee($matching->title)
            ->assertDontSee('OCR 처리 사례');
    }
}
