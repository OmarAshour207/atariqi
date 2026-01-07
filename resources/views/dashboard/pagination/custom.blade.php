@if ($paginator->hasPages())
    <ul class="pagination justify-content-end ">
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <a class="page-link" href="{{ \Request::url() }}" aria-label="Previous">
                    <span aria-hidden="true" class="material-icons">{{ session('locale') == 'ar' ? 'last_page' : 'first_page' }}</span>
                    <span class="sr-only">First</span>
                </a>
            </li>

            <li class="page-item disabled">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true" class="material-icons">chevron_{{ session('locale') == 'ar' ? 'right' : 'left' }}</span>
                    <span class="sr-only">Prev</span>
                </a>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ \Request::url() }}" aria-label="Previous">
                    <span aria-hidden="true" class="material-icons">{{ session('locale') == 'ar' ? 'last_page' : 'first_page' }}</span>
                    <span class="sr-only">First</span>
                </a>
            </li>

            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true" class="material-icons">chevron_{{ session('locale') == 'ar' ? 'right' : 'left' }}</span>
                    <span class="sr-only">Prev</span>
                </a>
            </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active">
                            <a class="page-link" href="#" aria-label="{{ $page }}">
                                <span>{{ $page }}</span>
                            </a>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}" aria-label="{{ $page }}">
                                <span>{{ $page }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                    <span class="sr-only">Next</span>
                    <span aria-hidden="true" class="material-icons">chevron_{{ session('locale') == 'ar' ? 'left' : 'right' }}</span>
                </a>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Next">
                    <span class="sr-only">Next</span>
                    <span aria-hidden="true" class="material-icons">chevron_{{ session('locale') == 'ar' ? 'left' : 'right' }}</span>
                </a>
            </li>
        @endif

        <li class="page-item">
            <a class="page-link" href="{{ \Request::url().'?page='.$paginator->lastPage() }}" aria-label="Next">
                <span class="sr-only">Last</span>
                <span aria-hidden="true" class="material-icons">{{ session('locale') == 'ar' ? 'first_page' : 'last_page' }}</span>
            </a>
        </li>
    </ul>
@endif
