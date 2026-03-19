@if ($paginator->hasPages())
    <div class="pager">
        @if ($paginator->onFirstPage())
            <span class="button secondary small disabled">이전</span>
        @else
            <a class="button secondary small" href="{{ $paginator->previousPageUrl() }}">이전</a>
        @endif

        <span class="pager-info">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="button secondary small" href="{{ $paginator->nextPageUrl() }}">다음</a>
        @else
            <span class="button secondary small disabled">다음</span>
        @endif
    </div>
@endif
