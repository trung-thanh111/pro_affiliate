@extends('frontend.homepage.layout')

@section('content')
    <div id="art-catalogue" class="page-body">
        {{-- Breadcrumb --}}
        <x-breadcrumb :breadcrumb="$breadcrumb" />

        <div class="art-catalogue-wrapper">
            <div class="uk-container uk-container-center">
                <h2 class="heading-2"><span>{{ $postCatalogue->name }}</span></h2>
                <div class="uk-grid uk-grid-medium">
                    @foreach($posts as $key => $val)
                    @php
                        $title = $val->languages->first()->pivot->name;
                        $image = $val->image;
                        $href = write_url($val->languages->first()->pivot->canonical);
                        $description = cutnchar(strip_tags($val->languages->first()->pivot->description), 450);
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
             
                </div>
            </div>
        </div>
    </div>
@endsection
