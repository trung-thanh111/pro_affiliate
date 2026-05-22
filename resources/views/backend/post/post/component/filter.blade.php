<form action="{{ route('post.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            @include('backend.dashboard.component.perpage')
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @php
                        $postCatalogueId = request('post_catalogue_id') ?: old('post_catalogue_id');
                    @endphp
                    <select name="post_catalogue_id" class="form-control setupSelect2 ml10">
                        @foreach($dropdown as $key => $val)
                        <option {{ ($postCatalogueId == $key)  ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route('post.import') }}" class="btn btn-success ml10"><i class="fa fa-upload mr5"></i> Import JSON</a>
                    <a href="{{ route('post.create') }}" class="btn btn-danger ml10"><i class="fa fa-plus mr5"></i>{{ __('messages.post.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>

