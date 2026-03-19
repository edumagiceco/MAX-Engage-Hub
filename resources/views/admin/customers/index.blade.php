@extends('layouts.app')

@section('title', 'Customers | MAX Engage Hub')

@section('content')
    <section class="section-header">
        <div>
            <p class="eyebrow">Customer Directory</p>
            <h1>고객 레코드와 최근 접점을 빠르게 조회합니다.</h1>
        </div>
    </section>

    <section class="panel">
        <form method="GET" class="filters">
            <label>
                <span>검색</span>
                <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="이름, 이메일, 회사명">
            </label>

            <label>
                <span>상태</span>
                <select name="status">
                    <option value="">전체</option>
                    @foreach ($statusOptions as $statusOption)
                        <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>
                            {{ $statusOption }}
                        </option>
                    @endforeach
                </select>
            </label>

            <button type="submit" class="button primary small">필터 적용</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>고객</th>
                    <th>회사</th>
                    <th>상태</th>
                    <th>최근 접점</th>
                    <th>생성일</th>
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
                            @if ($customer->latestActivity?->summaryLine())
                                <small>{{ $customer->latestActivity->summaryLine() }}</small>
                            @endif
                        </td>
                        <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">조건에 맞는 고객이 없습니다.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @include('partials.pagination', ['paginator' => $customers])
    </section>
@endsection
