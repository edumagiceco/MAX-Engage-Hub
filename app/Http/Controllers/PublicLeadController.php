<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadIntakeRequest;
use App\Services\LeadIntakeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicLeadController extends Controller
{
    public function __construct(
        private readonly LeadIntakeService $leadIntakeService,
    ) {
    }

    public function home(): View
    {
        return view('home', [
            'forms' => $this->forms(),
        ]);
    }

    public function show(string $form): View
    {
        return view('public.form', [
            'form' => $this->form($form),
        ]);
    }

    public function store(StoreLeadIntakeRequest $request, string $form): RedirectResponse
    {
        $config = $this->form($form);
        $customer = $this->leadIntakeService->store($form, $request->validated());

        return redirect()
            ->route($config['route'])
            ->with('status', "{$customer->name}님의 {$config['label']} 요청이 저장되었습니다.");
    }

    private function form(string $key): array
    {
        $forms = $this->forms();

        abort_unless(isset($forms[$key]), 404);

        return $forms[$key];
    }

    private function forms(): array
    {
        return [
            'contact' => [
                'key' => 'contact',
                'label' => '일반 문의',
                'headline' => '첫 문의를 바로 리드로 전환합니다.',
                'description' => '관심 주제와 현재 상황을 함께 남기면 운영 인박스와 고객 상세에 즉시 반영됩니다.',
                'helper' => '소개받고 싶은 서비스, 현재 고민, 원하는 미팅 방식 등을 적어주세요.',
                'route' => 'forms.contact',
                'show_assessment_fields' => false,
            ],
            'education' => [
                'key' => 'education',
                'label' => '교육 문의',
                'headline' => '교육 운영에 필요한 기본 맥락을 한 번에 받습니다.',
                'description' => '회사, 대상자, 교육 목적을 남기면 교육 제안과 후속 운영에 바로 연결됩니다.',
                'helper' => '희망 주제, 예상 대상자 수, 진행 시기 등을 적어주세요.',
                'route' => 'forms.education',
                'show_assessment_fields' => false,
            ],
            'diagnosis' => [
                'key' => 'diagnosis',
                'label' => 'AX 진단',
                'headline' => '진단 요청을 놓치지 않고 고객 기록에 연결합니다.',
                'description' => '현재 단계와 병목을 남기면 이후 상담과 추천 설계에 재사용할 수 있습니다.',
                'helper' => '현재 AX 도입 수준, 우선 해결 과제, 관련 팀 규모 등을 적어주세요.',
                'route' => 'forms.diagnosis',
                'show_assessment_fields' => true,
            ],
            'recommendation' => [
                'key' => 'recommendation',
                'label' => 'AX 맞춤 추천',
                'headline' => '추천 요청을 후속 제안 흐름으로 바로 넘깁니다.',
                'description' => '관심 영역과 현업 문제를 남기면 추천 요청 이력과 고객 상태가 함께 저장됩니다.',
                'helper' => '관심 솔루션, 도입 목적, 원하는 결과를 적어주세요.',
                'route' => 'forms.recommendation',
                'show_assessment_fields' => true,
            ],
        ];
    }
}
