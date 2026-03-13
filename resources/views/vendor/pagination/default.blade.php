@if ($paginator->hasPages())
    <div class="pager">
        <ul class="pager-list">
            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <li class="pager-item disabled">‹</li>
            @else
                <li class="pager-item">
                    <a href="{{ $paginator->previousPageUrl() }}">‹</a>
                </li>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pager-item active">
                                <span>{{ $page }}</span>
                            </li>
                        @else
                            <li class="pager-item">
                                <a href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="pager-item">
                    <a href="{{ $paginator->nextPageUrl() }}">›</a>
                </li>
            @else
                <li class="pager-item disabled">›</li>
            @endif
        </ul>
    </div>
<style>


</style>
@endif
