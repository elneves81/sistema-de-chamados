@props(['paginator', 'label' => 'registros'])

@if($paginator->hasPages())
<div class="pagination-wrapper">
    <div class="pagination-info">
        <small class="text-muted">
            Mostrando <span class="fw-semibold">{{ $paginator->firstItem() ?? 0 }}</span> a 
            <span class="fw-semibold">{{ $paginator->lastItem() ?? 0 }}</span> 
            de <span class="fw-semibold">{{ $paginator->total() }}</span> {{ $label }}
        </small>
    </div>
    <div>
        {{ $paginator->appends(request()->query())->links() }}
    </div>
</div>
@endif
