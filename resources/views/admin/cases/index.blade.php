@extends('layouts.app')

@section('title', 'Case Library | MAX Engage Hub')

@section('content')
    <section class="section-header">
        <div>
            <p class="eyebrow">Case Library</p>
            <h1>상담에 바로 붙일 수 있는 사례를 검색하고 등록합니다.</h1>
        </div>
    </section>

    <section class="split split-wide">
        <div class="panel">
            <h2>사례 등록</h2>

            <form method="POST" action="{{ route('admin.cases.store') }}" class="stack">
                @csrf

                <div class="field-grid">
                    <label>
                        <span>제목</span>
                        <input type="text" name="title" value="{{ old('title') }}" required>
                    </label>

                    <label>
                        <span>솔루션 타입</span>
                        <select name="solution_type" required>
                            @foreach ($solutionTypes as $solutionType)
                                <option value="{{ $solutionType }}" @selected(old('solution_type') === $solutionType)>{{ $solutionType }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>산업</span>
                        <input type="text" name="industry" value="{{ old('industry') }}">
                    </label>

                    <label>
                        <span>부서</span>
                        <input type="text" name="department" value="{{ old('department') }}">
                    </label>
                </div>

                <label>
                    <span>문제 정의</span>
                    <input type="text" name="problem" value="{{ old('problem') }}" required>
                </label>

                <label>
                    <span>요약</span>
                    <textarea name="summary" rows="5" required>{{ old('summary') }}</textarea>
                </label>

                <label>
                    <span>성과 / 결과</span>
                    <textarea name="outcome" rows="4">{{ old('outcome') }}</textarea>
                </label>

                <label>
                    <span>지표</span>
                    <textarea name="metrics_input" rows="4" placeholder="예: 처리시간: 60% 단축&#10;정확도: 92%">{{ old('metrics_input') }}</textarea>
                </label>

                <label>
                    <span>상태</span>
                    <select name="status">
                        @foreach ($statusOptions as $statusOption)
                            <option value="{{ $statusOption }}" @selected(old('status', 'approved') === $statusOption)>{{ $statusOption }}</option>
                        @endforeach
                    </select>
                </label>

                <button type="submit" class="button primary">사례 등록</button>
            </form>
        </div>

        <div class="stack">
            <div class="panel">
                <h2>사례 검색</h2>

                <form method="GET" class="filters">
                    <label>
                        <span>검색</span>
                        <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="제목, 문제, 요약">
                    </label>

                    <label>
                        <span>산업</span>
                        <input type="text" name="industry" value="{{ $filters['industry'] }}">
                    </label>

                    <label>
                        <span>부서</span>
                        <input type="text" name="department" value="{{ $filters['department'] }}">
                    </label>

                    <label>
                        <span>솔루션</span>
                        <select name="solution_type">
                            <option value="">전체</option>
                            @foreach ($solutionTypes as $solutionType)
                                <option value="{{ $solutionType }}" @selected($filters['solution_type'] === $solutionType)>{{ $solutionType }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>상태</span>
                        <select name="status">
                            <option value="">전체</option>
                            @foreach ($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </label>

                    <button type="submit" class="button primary small">검색</button>
                </form>
            </div>

            <div class="panel">
                <div class="table-head">
                    <h2>등록된 사례</h2>
                    <span class="muted">{{ $cases->total() }}건</span>
                </div>

                <ul class="case-list">
                    @forelse ($cases as $case)
                        <li>
                            <div>
                                <strong>{{ $case->title }}</strong>
                                <p>{{ $case->summary }}</p>
                                <small>{{ $case->industry ?: '산업 미지정' }} · {{ $case->department ?: '부서 미지정' }} · {{ $case->problem }}</small>

                                @if ($case->metrics_json)
                                    <div class="metrics-row">
                                        @foreach ($case->metrics_json as $metric)
                                            <span class="metric-pill">{{ $metric['label'] }} {{ $metric['value'] }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="case-meta">
                                <span class="badge">{{ $case->solution_type }}</span>
                                <small>{{ $case->status }}</small>
                            </div>
                        </li>
                    @empty
                        <li class="empty">등록된 사례가 없습니다.</li>
                    @endforelse
                </ul>

                @include('partials.pagination', ['paginator' => $cases])
            </div>
        </div>
    </section>
@endsection
