<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadIntakeTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_creates_customer_and_activity(): void
    {
        $response = $this->post(route('forms.contact.store'), [
            'name' => 'Kim Demo',
            'email' => 'kim@example.com',
            'company_name' => 'MagicEcole',
            'message' => '첫 문의입니다.',
            'consent_marketing' => '1',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('forms.contact');

        $this->assertDatabaseHas('customers', [
            'email' => 'kim@example.com',
            'status' => 'new',
            'latest_source' => 'contact_form',
        ]);

        $customer = Customer::query()->where('email', 'kim@example.com')->firstOrFail();

        $this->assertDatabaseHas('lead_activities', [
            'customer_id' => $customer->id,
            'activity_type' => 'contact_submitted',
        ]);
    }

    public function test_second_submission_merges_into_existing_customer(): void
    {
        Customer::query()->create([
            'name' => 'Existing',
            'email' => 'merge@example.com',
            'status' => 'in_review',
        ]);

        $response = $this->post(route('forms.education.store'), [
            'name' => 'Existing Updated',
            'email' => 'merge@example.com',
            'company_name' => 'New Company',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('forms.education');

        $this->assertSame(1, Customer::query()->where('email', 'merge@example.com')->count());
        $this->assertDatabaseHas('customers', [
            'email' => 'merge@example.com',
            'company_name' => 'New Company',
            'status' => 'in_review',
            'latest_source' => 'education_form',
        ]);
        $this->assertDatabaseCount('lead_activities', 1);
    }
}
