@if ($paginator->hasPages())
    <ul class="pagination pagination-sm justify-content-end mb-0">
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">
                    <span class="material-icons">{{ session('locale') == 'ar' ? 'last_page' : 'first_page' }}</span>
                </span>
            </li>
            <li class="page-item disabled">
                <span class="page-link">
                    <span class="material-icons">chevron_{{ session('locale') == 'ar' ? 'right' : 'left' }}</span>
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="First">
                    <span class="material-icons">{{ session('locale') == 'ar' ? 'last_page' : 'first_page' }}</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <span class="material-icons">chevron_{{ session('locale') == 'ar' ? 'right' : 'left' }}</span>
                </a>
            </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                    <span class="material-icons">chevron_{{ session('locale') == 'ar' ? 'left' : 'right' }}</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="Last">
                    <span class="material-icons">{{ session('locale') == 'ar' ? 'first_page' : 'last_page' }}</span>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">
                    <span class="material-icons">chevron_{{ session('locale') == 'ar' ? 'left' : 'right' }}</span>
                </span>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="Last">
                    <span class="material-icons">{{ session('locale') == 'ar' ? 'first_page' : 'last_page' }}</span>
                </a>
            </li>
        @endif
    </ul>
@endif
