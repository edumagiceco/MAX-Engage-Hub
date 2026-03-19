@extends('layouts.app')

@section('title', $customer->name.' | MAX Engage Hub')

@section('content')
    <section class="section-header">
        <div>
            <p class="eyebrow">Customer Detail</p>
            <h1>{{ $customer->name }}</h1>
            <p class="muted">{{ $customer->email }} @if($customer->company_name) · {{ $customer->company_name }} @endif</p>
        </div>
    </section>

    <section class="detail-grid">
        <div class="stack">
            <div class="panel">
                <div class="table-head">
                    <h2>기본 정보</h2>
                    <span class="badge">{{ $customer->status }}</span>
                </div>

                <dl class="meta-list">
                    <div><dt>연락처</dt><dd>{{ $customer->phone ?: '-' }}</dd></div>
                    <div><dt>직책</dt><dd>{{ $customer->job_title ?: '-' }}</dd></div>
                    <div><dt>최근 유입</dt><dd>{{ $customer->latest_source ?: '-' }}</dd></div>
                    <div><dt>마케팅 동의</dt><dd>{{ $customer->consent_marketing ? '동의' : '미동의' }}</dd></div>
                </dl>
            </div>

            <div class="panel">
                <div class="table-head">
                    <h2>진단 및 추천 요약</h2>
                    <a class="button secondary small" href="{{ route('admin.cases.index') }}">사례 검색</a>
                </div>

                <div class="summary-grid">
                    <article class="insight-card">
                        <span class="card-tag">Diagnosis</span>
                        @if ($customer->latestDiagnosisResult)
                            <strong>{{ $customer->latestDiagnosisResult->overall_score }}점 · {{ $customer->latestDiagnosisResult->maturity_level }}</strong>
                            <p>{{ $customer->latestDiagnosisResult->summary }}</p>
                            <small>{{ $customer->latestDiagnosisResult->main_pain_points ?: '주요 pain point 미입력' }}</small>
                        @else
                            <strong>아직 진단 결과가 없습니다.</strong>
                            <p>AX 진단 요청이 들어오면 성숙도와 요약이 여기에 저장됩니다.</p>
                        @endif
                    </article>

                    <article class="insight-card">
                        <span class="card-tag">Recommendation</span>
                        @if ($customer->latestRecommendationResult)
                            <strong>{{ $customer->latestRecommendationResult->recommendation_type }}</strong>
                            <p>{{ $customer->latestRecommendationResult->recommendation_reason }}</p>
                            <small>{{ $customer->latestRecommendationResult->next_action }}</small>
                        @else
                            <strong>아직 추천 결과가 없습니다.</strong>
                            <p>맞춤 추천 요청이나 진단 이후 생성된 1차 제안이 표시됩니다.</p>
                        @endif
                    </article>
                </div>
            </div>

            <div class="panel">
                <h2>고객 태그</h2>

                <div class="chip-row">
                    @forelse ($customer->tags as $tag)
                        <form method="POST" action="{{ route('admin.customers.tags.destroy', [$customer, $tag]) }}" class="chip-form">
                            @csrf
                            @method('DELETE')
                            <span class="tag-chip">
                                {{ $tag->tag }}
                                <button type="submit" class="tag-delete" aria-label="태그 삭제">×</button>
                            </span>
                        </form>
                    @empty
                        <p class="muted">아직 연결된 태그가 없습니다.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('admin.customers.tags.store', $customer) }}" class="inline-form top-gap">
                    @csrf
                    <input type="text" name="tag" value="{{ old('tag') }}" placeholder="예: high_intent">
                    <button type="submit" class="button primary small">태그 추가</button>
                </form>
            </div>

            <div class="panel">
                <h2>상태 변경</h2>

                <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="inline-form">
                    @csrf
                    @method('PATCH')

                    <select name="status">
                        @foreach ($statusOptions as $statusOption)
                            <option value="{{ $statusOption }}" @selected($customer->status === $statusOption)>
                                {{ $statusOption }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="button primary small">업데이트</button>
                </form>
            </div>

            <div class="panel">
                <h2>운영 메모 추가</h2>

                <form method="POST" action="{{ route('admin.customers.notes.store', $customer) }}" class="stack">
                    @csrf
                    <textarea name="note" rows="5" placeholder="상담 준비 메모, 다음 연락 포인트 등을 남겨주세요.">{{ old('note') }}</textarea>
                    <button type="submit" class="button primary small">메모 저장</button>
                </form>
            </div>

            <div class="panel">
                <h2>후속 액션 등록</h2>

                <form method="POST" action="{{ route('admin.customers.tasks.store', $customer) }}" class="stack">
                    @csrf

                    <label>
                        <span>액션 제목</span>
                        <input type="text" name="title" value="{{ old('title') }}" required>
                    </label>

                    <label>
                        <span>예정일</span>
                        <input type="date" name="due_date" value="{{ old('due_date') }}">
                    </label>

                    <label>
                        <span>메모</span>
                        <textarea name="note" rows="4">{{ old('note') }}</textarea>
                    </label>

                    <button type="submit" class="button primary small">후속 액션 저장</button>
                </form>
            </div>
        </div>

        <div class="stack">
            <div class="panel">
                <h2>활동 타임라인</h2>

                <ul class="timeline">
                    @forelse ($customer->activities as $activity)
                        <li>
                            <div>
                                <strong>{{ $activity->title }}</strong>
                                <p>{{ $activity->activity_type }} · {{ $activity->source }}</p>
                            </div>
                            <time>{{ $activity->created_at->format('Y-m-d H:i') }}</time>
                        </li>
                    @empty
                        <li class="empty">저장된 활동 이력이 없습니다.</li>
                    @endforelse
                </ul>
            </div>

            <div class="panel">
                <h2>운영 메모</h2>

                <ul class="timeline">
                    @forelse ($customer->notes as $note)
                        <li>
                            <div>
                                <strong>{{ $note->user?->name ?: 'System' }}</strong>
                                <p>{{ $note->note }}</p>
                            </div>
                            <time>{{ $note->created_at->format('Y-m-d H:i') }}</time>
                        </li>
                    @empty
                        <li class="empty">운영 메모가 없습니다.</li>
                    @endforelse
                </ul>
            </div>

            <div class="panel">
                <h2>후속 액션</h2>

                <ul class="timeline">
                    @forelse ($customer->followUpTasks as $task)
                        <li>
                            <div>
                                <strong>{{ $task->title }}</strong>
                                <p>{{ $task->note ?: '메모 없음' }}</p>
                            </div>
                            <time>
                                {{ $task->status }}
                                @if ($task->due_date)
                                    · {{ $task->due_date->format('Y-m-d') }}
                                @endif
                            </time>
                        </li>
                    @empty
                        <li class="empty">등록된 후속 액션이 없습니다.</li>
                    @endforelse
                </ul>
            </div>

            <div class="panel">
                <div class="table-head">
                    <h2>관련 사례</h2>
                    <a class="button secondary small" href="{{ route('admin.cases.index', ['solution_type' => $customer->latestRecommendationResult?->recommendation_type]) }}">더 보기</a>
                </div>

                <ul class="case-list">
                    @forelse ($relatedCases as $case)
                        <li>
                            <div>
                                <strong>{{ $case->title }}</strong>
                                <p>{{ $case->problem }}</p>
                                <small>{{ $case->industry ?: '산업 미지정' }} · {{ $case->department ?: '부서 미지정' }}</small>
                            </div>
                            <div class="case-meta">
                                <span class="badge">{{ $case->solution_type }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="empty">추천 타입과 연결된 사례가 아직 없습니다.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </section>
@endsection
