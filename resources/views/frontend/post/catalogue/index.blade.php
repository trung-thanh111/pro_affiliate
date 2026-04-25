@extends('frontend.homepage.layout')

@section('content')
    <div id="art-catalogue" class="page-body">
        {{-- Breadcrumb --}}
        <div class="uk-container uk-container-center">
            <x-breadcrumb :breadcrumb="$breadcrumb" />
        </div>

        <div class="art-catalogue-wrapper">
            <div class="uk-container uk-container-center">
                @php
                    $catalogueName = $postCatalogue->languages->first()->pivot->name ?? $postCatalogue->name ?? '';
                    if(request()->has('keyword')) {
                        $catalogueName = 'Kết quả tìm kiếm cho: ' . request('keyword');
                    }
                @endphp
                <h2 class="heading-2"><span>{{ $catalogueName }}</span></h2>
                <div class="uk-grid uk-grid-medium">
                    @if(count($posts))
                        @foreach($posts as $key => $val)
                        @php
                            $title = $val->languages->first()->pivot->name ?? $val->name;
                            $image = $val->image;
                            $href = write_url($val->languages->first()->pivot->canonical ?? '');
                            $description = cutnchar(strip_tags($val->languages->first()->pivot->description ?? ''), 450);
                        @endphp

                        <div class="uk-width-1-1 uk-width-small-1-1 uk-width-medium-1-3 uk-width-large-1-4 mb25">
                            <div class="news-item wow fadeInUp" data-wow-delay="{{ ($key % 4) * 0.1 }}s">
                                <a href="{{ $href }}" class="image img-cover" title="{{ $title }}">
                                    <img src="{{ $image }}" alt="{{ $title }}">
                                </a>
                                <div class="info">
                                    <h3 class="title">
                                        <a href="{{ $href }}" title="{{ $title }}">{{ $title }}</a>
                                    </h3>
                                    <div class="description">
                                        {!! $description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="uk-width-1-1">
                            <div class="no-results text-center py-5">
                                <i class="bi bi-search display-1 text-muted mb-3 d-block"></i>
                                <h3 class="text-muted">Không tìm thấy bài viết nào phù hợp với từ khóa của bạn.</h3>
                                <p class="text-secondary">Vui lòng thử lại với từ khóa khác.</p>
                                <a href="{{ route('home.index') }}" class="btn btn-primary-gradient px-4 py-2 mt-3 text-white text-decoration-none rounded-pill">Về trang chủ</a>
                            </div>
                        </div>
                    @endif
                </div>

                @if(method_exists($posts, 'links'))
                    <div class="uk-margin-large-top uk-text-center">
                        {{ $posts->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
