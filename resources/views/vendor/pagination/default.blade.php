@if ($paginator->hasPages())
    <div class="pager">
        <ul class="pager-list">

            {{-- 맨 처음 --}}
            @if ($paginator->currentPage() > 1)
                <li class="pager-item">
                    <a href="{{ $paginator->url(1) }}">«</a>
                </li>
            @endif

            {{-- 이전 --}}
            @if ($paginator->onFirstPage())
                <li class="pager-item disabled">‹</li>
            @else
                <li class="pager-item">
                    <a href="{{ $paginator->previousPageUrl() }}">‹</a>
                </li>
            @endif

            {{-- 10개 블럭 계산 --}}
            @php
                $blockSize = 10;
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();

                $currentBlock = ceil($currentPage / $blockSize);
                $start = ($currentBlock - 1) * $blockSize + 1;
                $end = min($start + $blockSize - 1, $lastPage);
            @endphp

            {{-- 페이지 번호 --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $currentPage)
                    <li class="pager-item active">
                        <span>{{ $i }}</span>
                    </li>
                @else
                    <li class="pager-item">
                        <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            {{-- 다음 --}}
            @if ($paginator->hasMorePages())
                <li class="pager-item">
                    <a href="{{ $paginator->nextPageUrl() }}">›</a>
                </li>
            @else
                <li class="pager-item disabled">›</li>
            @endif

            {{-- 맨 마지막 --}}
            @if ($paginator->currentPage() < $paginator->lastPage())
                <li class="pager-item">
                    <a href="{{ $paginator->url($paginator->lastPage()) }}">»</a>
                </li>
            @endif

        </ul>
    </div>
@endif
