<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminConsoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_admin_pages(): void
    {
        $this->get(route('admin.inbox'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_add_note_and_follow_up_task(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);

        $customer = Customer::query()->create([
            'name' => 'Lee Demo',
            'email' => 'lee@example.com',
            'status' => 'new',
        ]);

        $this->actingAs($user)->post(route('admin.customers.notes.store', $customer), [
            'note' => '첫 상담 전에 유사 사례 준비 필요',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('admin.customers.tasks.store', $customer), [
            'title' => '첫 미팅 일정 확정',
            'due_date' => '2026-03-21',
        ])->assertRedirect();

        $this->assertDatabaseHas('customer_notes', [
            'customer_id' => $customer->id,
            'note' => '첫 상담 전에 유사 사례 준비 필요',
        ]);

        $this->assertDatabaseHas('follow_up_tasks', [
            'customer_id' => $customer->id,
            'title' => '첫 미팅 일정 확정',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_view_submitted_input_details_on_customer_pages(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
        ]);

        $customer = Customer::query()->create([
            'name' => 'Han Demo',
            'email' => 'han@example.com',
            'phone' => '010-1111-2222',
            'company_name' => 'Magicsoft',
            'job_title' => 'AX Lead',
            'status' => 'new',
            'latest_source' => 'diagnosis_form',
        ]);

        LeadActivity::query()->create([
            'customer_id' => $customer->id,
            'activity_type' => 'diagnosis_completed',
            'source' => 'diagnosis_form',
            'title' => 'AX 진단 요청이 접수되었습니다.',
            'payload_json' => [
                'form_type' => 'diagnosis',
                'submitted_name' => 'Han Demo',
                'submitted_email' => 'han@example.com',
                'submitted_phone' => '010-1111-2222',
                'submitted_company_name' => 'Magicsoft',
                'submitted_job_title' => 'AX Lead',
                'details' => '문서 검색 시간이 길고 반복 응답 품질이 흔들립니다.',
                'interest_area' => 'rag',
                'current_stage' => 'pilot',
                'team_size' => '12명',
                'consent_marketing' => true,
            ],
        ]);

        $this->actingAs($user)
            ->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee('최근 입력 정보')
            ->assertSee('AX 진단')
            ->assertSee('han@example.com')
            ->assertSee('문서 검색 시간이 길고 반복 응답 품질이 흔들립니다.')
            ->assertSee('RAG')
            ->assertSee('파일럿 진행');

        $this->actingAs($user)
            ->get(route('admin.inbox'))
            ->assertOk()
            ->assertSee('문서 검색 시간이 길고 반복 응답 품질이 흔들립니다.');
    }
}
