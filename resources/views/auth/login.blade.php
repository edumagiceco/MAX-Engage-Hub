@extends('layouts.app')

@section('title', 'Admin Login | MAX Engage Hub')

@section('content')
    <section class="auth-wrap">
        <div class="panel accent">
            <p class="eyebrow">Admin Console</p>
            <h1>운영 인박스와 고객 상세에 로그인합니다.</h1>
            <p>닷홈 무료호스팅 배포를 전제로 한 경량 운영 화면입니다. 세션 기반 인증만 사용합니다.</p>
        </div>

        <div class="panel auth-card">
            <form method="POST" action="{{ route('login.store') }}" class="stack">
                @csrf

                <label>
                    <span>이메일</span>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </label>

                <label>
                    <span>비밀번호</span>
                    <input type="password" name="password" required>
                </label>

                <button type="submit" class="button primary">로그인</button>
            </form>
        </div>
    </section>
@endsection
