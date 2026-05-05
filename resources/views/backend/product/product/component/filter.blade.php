<form action="{{ route('product.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @php
                        $productCatalogueId = request('product_catalogue_id') ?: old('product_catalogue_id');
                    @endphp
                    <select name="product_catalogue_id" class="form-control setupSelect2 ml10">
                        @foreach ($dropdown as $key => $val)
                            <option {{ $productCatalogueId == $key ? 'selected' : '' }} value="{{ $key }}">
                                {{ $val }}</option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <input type="file" id="import-json-input" accept=".json" style="display: none;">
                    <button type="button" id="import-json-btn" class="btn btn-success mr10">
                        <i class="fa fa-file-code-o mr5"></i> Import data
                    </button>
                    <a href="{{ route('product.create') }}" class="btn btn-danger"><i
                            class="fa fa-plus mr5"></i>{{ $config['seo']['create']['title'] }}</a>
                </div>
            </div>
        </div>
    </div>
</form>
