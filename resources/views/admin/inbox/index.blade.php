@extends('layouts.app')

@section('title', 'Inbox | MAX Engage Hub')

@section('content')
    <section class="section-header">
        <div>
            <p class="eyebrow">Admin Inbox</p>
            <h1>신규 리드와 후속 대응 대상을 한 화면에서 봅니다.</h1>
        </div>
    </section>

    <section class="stats">
        <div class="stat-card">
            <span>New</span>
            <strong>{{ $summary['new'] }}</strong>
        </div>
        <div class="stat-card">
            <span>In Review</span>
            <strong>{{ $summary['in_review'] }}</strong>
        </div>
        <div class="stat-card">
            <span>Contacted</span>
            <strong>{{ $summary['contacted'] }}</strong>
        </div>
        <div class="stat-card">
            <span>Total</span>
            <strong>{{ $summary['total'] }}</strong>
        </div>
    </section>

    <section class="panel">
        <div class="table-head">
            <h2>Lead Queue</h2>
            <a class="button secondary small" href="{{ route('admin.customers.index') }}">전체 고객 보기</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>고객</th>
                    <th>회사</th>
                    <th>상태</th>
                    <th>최근 활동</th>
                    <th>유입</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a>
                            <small>{{ $customer->email }}</small>
                        </td>
                        <td>{{ $customer->company_name ?: '-' }}</td>
                        <td><span class="badge">{{ $customer->status }}</span></td>
                        <td>
                            {{ $customer->latestActivity?->title ?: '-' }}
                            <small>{{ optional($customer->latestActivity?->created_at)->format('Y-m-d H:i') }}</small>
                            @if ($customer->latestActivity?->summaryLine())
                                <small>{{ $customer->latestActivity->summaryLine() }}</small>
                            @endif
                        </td>
                        <td>{{ $customer->latest_source ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">아직 저장된 리드가 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @include('partials.pagination', ['paginator' => $customers])
    </section>
@endsection
