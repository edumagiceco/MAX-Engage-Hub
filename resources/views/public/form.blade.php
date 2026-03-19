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
                        <input type="text" name="interest_area" value="{{ old('interest_area') }}">
                    </label>

                    <label>
                        <span>현재 단계</span>
                        <input type="text" name="current_stage" value="{{ old('current_stage') }}">
                    </label>

                    <label>
                        <span>관련 팀 규모</span>
                        <input type="text" name="team_size" value="{{ old('team_size') }}">
                    </label>
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
