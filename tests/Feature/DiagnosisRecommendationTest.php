<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiagnosisRecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_diagnosis_submission_creates_diagnosis_recommendation_and_tags(): void
    {
        $response = $this->post(route('forms.diagnosis.store'), [
            'name' => 'Park Demo',
            'email' => 'park@example.com',
            'company_name' => 'Demo Co',
            'interest_area' => 'rag',
            'current_stage' => 'pilot',
            'team_size' => '12명',
            'details' => '운영 문서가 흩어져 있고 답변 준비 시간이 길어 상담 전 자료 정리가 어렵습니다.',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('forms.diagnosis');

        $customer = Customer::query()->where('email', 'park@example.com')->firstOrFail();

        $this->assertDatabaseHas('diagnosis_results', [
            'customer_id' => $customer->id,
            'maturity_level' => 'scaling',
        ]);

        $this->assertDatabaseHas('recommendation_results', [
            'customer_id' => $customer->id,
            'recommendation_type' => 'rag',
        ]);

        $this->assertDatabaseHas('customer_tags', [
            'customer_id' => $customer->id,
            'tag' => 'rag_interest',
        ]);

        $this->assertDatabaseHas('customer_tags', [
            'customer_id' => $customer->id,
            'tag' => 'high_intent',
        ]);
    }

    public function test_recommendation_request_creates_recommendation_without_diagnosis_snapshot(): void
    {
        $this->post(route('forms.recommendation.store'), [
            'name' => 'Choi Demo',
            'email' => 'choi@example.com',
            'interest_area' => 'workflow_automation',
            'current_stage' => 'exploring',
        ])->assertRedirectToRoute('forms.recommendation');

        $customer = Customer::query()->where('email', 'choi@example.com')->firstOrFail();

        $this->assertDatabaseCount('diagnosis_results', 0);
        $this->assertDatabaseHas('recommendation_results', [
            'customer_id' => $customer->id,
            'recommendation_type' => 'workflow_automation',
        ]);
    }
}
