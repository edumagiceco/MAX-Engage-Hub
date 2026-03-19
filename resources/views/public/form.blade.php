@extends('layouts.app')

@section('title', $form['label'].' | MAX Engage Hub')

@section('content')
    <section class="split">
        <div class="panel accent">
            <p class="eyebrow">{{ $form['label'] }}</p>
            <h1>{{ $form['headline'] }}</h1>
            <p>{{ $form['description'] }}</p>
            <p class="muted">{{ $form['helper'] }}</p>
        </div>

        <div class="panel">
            <form method="POST" action="{{ route($form['route'].'.store') }}" class="stack">
                @csrf

                <div class="field-grid">
                    <label>
                        <span>이름</span>
                        <input type="text" name="name" value="{{ old('name') }}" required>
                    </label>

                    <label>
                        <span>이메일</span>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </label>

                    <label>
                        <span>연락처</span>
                        <input type="text" name="phone" value="{{ old('phone') }}">
                    </label>

                    <label>
                        <span>회사명</span>
                        <input type="text" name="company_name" value="{{ old('company_name') }}">
                    </label>

                    <label>
                        <span>직책</span>
                        <input type="text" name="job_title" value="{{ old('job_title') }}">
                    </label>

                    <label>
                        <span>관심 영역</span>
                        <select name="interest_area">
                            <option value="">선택</option>
                            @foreach (['education' => '교육', 'consulting' => '컨설팅', 'rag' => 'RAG', 'ocr' => 'OCR', 'workflow_automation' => '업무 자동화'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('interest_area') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    @if ($form['show_assessment_fields'])
                        <label>
                            <span>현재 단계</span>
                            <select name="current_stage">
                                <option value="">선택</option>
                                @foreach (['exploring' => '검토 중', 'pilot' => '파일럿', 'rollout' => '부서 확산', 'companywide' => '전사 확산'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('current_stage') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            <span>관련 팀 규모</span>
                            <input type="text" name="team_size" value="{{ old('team_size') }}" placeholder="예: 5명, 20명">
                        </label>
                    @endif
                </div>

                <label>
                    <span>한 줄 요약</span>
                    <input type="text" name="message" value="{{ old('message') }}">
                </label>

                <label>
                    <span>상세 내용</span>
                    <textarea name="details" rows="7">{{ old('details') }}</textarea>
                </label>

                <label class="checkbox">
                    <input type="checkbox" name="consent_marketing" value="1" @checked(old('consent_marketing'))>
                    <span>블로그/교육/AX 콘텐츠 관련 안내 메일 수신에 동의합니다.</span>
                </label>

                <div class="actions">
                    <button type="submit" class="button primary">저장하기</button>
                    <a href="{{ route('home') }}" class="button secondary">홈으로</a>
                </div>
            </form>
        </div>
    </section>
@endsection
