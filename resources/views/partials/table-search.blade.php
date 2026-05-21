@php
    $placeholder = $placeholder ?? 'Search table...';
    $route = $route ?? url()->current();
    $preserve = collect(request()->except(['q', 'page']))
        ->filter(fn ($value) => $value !== null && $value !== '');
@endphp

<form action="{{ $route }}" method="GET" class="mb-3">
    @foreach($preserve as $key => $value)
        @if(is_array($value))
            @foreach($value as $item)
                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    <div class="input-group">
        <input type="search" name="q" class="form-control" value="{{ request('q') }}" placeholder="{{ $placeholder }}">
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit" title="Search">
                <i class="fas fa-search"></i>
            </button>
            @if(request('q'))
                <a href="{{ $route }}{{ $preserve->isNotEmpty() ? '?' . http_build_query($preserve->all()) : '' }}" class="btn btn-outline-secondary" title="Clear search">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </div>
</form>
