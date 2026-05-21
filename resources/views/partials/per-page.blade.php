<form method="GET" class="form-inline per-page-form">
    @foreach(request()->except('per_page', 'page') as $key => $value)
        @if(is_array($value))
            @foreach($value as $item)
                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    <label for="per_page" class="mr-2 mb-0">Show</label>
    <select id="per_page" name="per_page" class="form-control form-control-sm" onchange="this.form.submit()">
        @foreach(\App\Support\TablePerPage::OPTIONS as $option)
            <option value="{{ $option }}" {{ (int) request('per_page', 10) === $option ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    </select>
    <span class="ml-2">entries</span>
</form>
