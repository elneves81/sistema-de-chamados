@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <ul class="pagination pagination-simple">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link page-icon">
                        <i class="bi bi-chevron-left me-1"></i>
                        Anterior
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link page-icon" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left me-1"></i>
                        Anterior
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link page-icon" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        Próximo
                        <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link page-icon">
                        Próximo
                        <i class="bi bi-chevron-right ms-1"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
    .pagination-simple .page-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #495057;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
    }

    .pagination-simple .page-icon:hover {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
        transform: translateY(-1px);
    }

    .pagination-simple .page-item.disabled .page-icon {
        background: #f8f9fa;
        color: #adb5bd;
        cursor: not-allowed;
        transform: none;
    }

    .pagination-simple .page-icon i {
        font-size: 12px;
    }
    </style>
@endif
