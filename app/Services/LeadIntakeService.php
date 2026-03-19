<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LeadIntakeService
{
    public function store(string $form, array $attributes): Customer
    {
        $config = $this->formConfig($form);

        return DB::transaction(function () use ($attributes, $config, $form) {
            $customer = Customer::query()->where('email', $attributes['email'])->first();

            $customerData = [
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'phone' => $attributes['phone'] ?? null,
                'company_name' => $attributes['company_name'] ?? null,
                'job_title' => $attributes['job_title'] ?? null,
                'consent_marketing' => (bool) ($attributes['consent_marketing'] ?? false),
                'latest_source' => $config['source'],
            ];

            if ($customer) {
                $customer->fill([
                    'name' => $customerData['name'] ?: $customer->name,
                    'phone' => $customerData['phone'] ?: $customer->phone,
                    'company_name' => $customerData['company_name'] ?: $customer->company_name,
                    'job_title' => $customerData['job_title'] ?: $customer->job_title,
                    'consent_marketing' => $customer->consent_marketing || $customerData['consent_marketing'],
                    'latest_source' => $customerData['latest_source'],
                ]);
                $customer->save();
            } else {
                $customer = Customer::query()->create([
                    ...$customerData,
                    'status' => 'new',
                ]);
            }

            $customer->activities()->create([
                'activity_type' => $config['activity_type'],
                'source' => $config['source'],
                'title' => $config['title'],
                'payload_json' => Arr::whereNotNull([
                    'form_type' => $form,
                    'message' => $attributes['message'] ?? null,
                    'details' => $attributes['details'] ?? null,
                    'interest_area' => $attributes['interest_area'] ?? null,
                    'current_stage' => $attributes['current_stage'] ?? null,
                    'team_size' => $attributes['team_size'] ?? null,
                    'submitted_name' => $attributes['name'],
                    'submitted_company_name' => $attributes['company_name'] ?? null,
                    'submitted_job_title' => $attributes['job_title'] ?? null,
                ]),
            ]);

            return $customer->fresh(['latestActivity']);
        });
    }

    public function formConfig(string $form): array
    {
        return match ($form) {
            'contact' => [
                'activity_type' => 'contact_submitted',
                'source' => 'contact_form',
                'title' => '일반 문의가 접수되었습니다.',
            ],
            'education' => [
                'activity_type' => 'education_inquiry_submitted',
                'source' => 'education_form',
                'title' => '교육 문의가 접수되었습니다.',
            ],
            'diagnosis' => [
                'activity_type' => 'diagnosis_completed',
                'source' => 'diagnosis_form',
                'title' => 'AX 진단 요청이 접수되었습니다.',
            ],
            'recommendation' => [
                'activity_type' => 'recommendation_requested',
                'source' => 'recommendation_form',
                'title' => 'AX 맞춤 추천 요청이 접수되었습니다.',
            ],
            default => abort(404),
        };
    }
}
