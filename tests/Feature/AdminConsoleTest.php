<?php

namespace Tests\Feature;

use App\Models\Customer;
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
}
