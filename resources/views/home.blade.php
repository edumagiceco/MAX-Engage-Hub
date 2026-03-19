@extends('layouts.app')

@section('title', 'MAX Engage Hub')

@section('content')
    <section class="hero">
        <div>
            <p class="eyebrow">Lead Intake MVP</p>
            <h1>유입부터 첫 상담 준비까지 끊기지 않게 저장합니다.</h1>
            <p class="hero-copy">
                닷홈 무료호스팅 제약에 맞춘 경량 Laravel 운영 앱입니다. 퍼블릭 폼 제출은 모두 고객 레코드와 활동 이력으로
                저장되고, 관리자는 인박스와 고객 상세에서 바로 확인할 수 있습니다.
            </p>
            <div class="hero-actions">
                <a class="button primary" href="{{ route('forms.contact') }}">문의 폼 열기</a>
                <a class="button secondary" href="{{ route('login') }}">관리자 로그인</a>
            </div>
        </div>

        <div class="hero-panel">
            <p>현재 구현 범위</p>
            <ul class="check-list">
                <li>일반 문의, 교육 문의, 진단, 추천 요청 수집</li>
                <li>이메일 기준 고객 중복 병합</li>
                <li>관리자 인박스 및 고객 상세</li>
                <li>운영 메모와 후속 액션 등록</li>
            </ul>
        </div>
    </section>

    <section class="section-header">
        <div>
            <p class="eyebrow">Public Entry Points</p>
            <h2>폼은 분리하되 데이터는 하나로 모읍니다.</h2>
        </div>
    </section>

    <section class="card-grid">
        @foreach ($forms as $form)
            <article class="card">
                <p class="card-tag">{{ $form['label'] }}</p>
                <h3>{{ $form['headline'] }}</h3>
                <p>{{ $form['description'] }}</p>
                <a class="card-link" href="{{ route($form['route']) }}">폼으로 이동</a>
            </article>
        @endforeach
    </section>
@endsection
