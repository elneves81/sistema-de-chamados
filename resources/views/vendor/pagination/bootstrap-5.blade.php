@if ($paginator->hasPages())
    <nav class="d-flex justify-items-center justify-content-between">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="bi bi-chevron-left" style="font-size: 14px;"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <i class="bi bi-chevron-left" style="font-size: 14px;"></i>
                        </a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            <i class="bi bi-chevron-right" style="font-size: 14px;"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="bi bi-chevron-right" style="font-size: 14px;"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
            <div>
                <p class="small text-muted">
                    {!! __('Showing') !!}
                    <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="fw-semibold">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <ul class="pagination pagination-custom">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link page-icon" aria-hidden="true">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link page-icon" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link page-number">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link page-number">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link page-number" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link page-icon" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link page-icon" aria-hidden="true">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <style>
    .pagination-custom {
        gap: 5px;
    }

    .pagination-custom .page-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #6c757d;
        font-size: 14px;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
    }

    .pagination-custom .page-icon:hover {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
        transform: translateY(-1px);
    }

    .pagination-custom .page-item.disabled .page-icon {
        background: #f8f9fa;
        color: #adb5bd;
        cursor: not-allowed;
        transform: none;
    }

    .pagination-custom .page-icon i {
        font-size: 12px;
        line-height: 1;
    }

    .pagination-custom .page-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #495057;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
    }

    .pagination-custom .page-number:hover {
        background: #e9ecef;
        color: #007bff;
        border-color: #007bff;
        transform: translateY(-1px);
    }

    .pagination-custom .page-item.active .page-number {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
        font-weight: 600;
    }

    .pagination-custom .page-item.disabled .page-number {
        background: #f8f9fa;
        color: #adb5bd;
        cursor: not-allowed;
        transform: none;
    }

    /* Mobile improvements */
    @media (max-width: 576px) {
        .pagination-custom .page-icon,
        .pagination-custom .page-number {
            width: 35px;
            height: 35px;
            font-size: 12px;
        }
        
        .pagination-custom .page-icon i {
            font-size: 10px;
        }
        
        .pagination-custom {
            gap: 3px;
        }
    }
    </style>
@endif
