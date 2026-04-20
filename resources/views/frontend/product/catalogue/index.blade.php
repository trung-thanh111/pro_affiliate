@extends('frontend.homepage.layout')

@section('content')
    <div id="prd-catalogue" class="page-body">
        <x-breadcrumb :breadcrumb="$breadcrumb" />
        <div class="uk-container uk-container-center uk-container">
            <div class="prd-catalogue-wrapper">
                <div class="uk-grid uk-grid-large">
                    <div class="uk-width-large-1-4">
                        <div class="sidebar-filter uk-panel">
                            <!-- Search -->
                            <div class="filter-block filter-search uk-margin-bottom">
                                <h4 class="filter-heading">TÌM KIẾM</h4>
                                <div class="uk-form-icon">
                                    <i class="uk-icon-search"></i>
                                    <input type="text" class="uk-form-small uk-width-1-1"
                                        placeholder="Nhập tên sản phẩm...">
                                </div>
                            </div>
                            <div class="filter-section">

                                <h3 class="filter-heading">DANH MỤC SẢN PHẨM</h3>

                                @include('frontend.product.catalogue.component.product-catalogue-tree', [
                                    'items' => $productCatalogues,
                                ])


                                <!-- PRICE -->
                                <h3 class="filter-heading">LỌC THEO KHOẢNG GIÁ</h3>
                                <ul class="uk-nav filter-list">
                                    <li>
                                        <label>
                                            <input type="checkbox" class="filter-price filtering" data-min="1000000"
                                                data-max="2000000">
                                            Từ 1 triệu - 2 triệu
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="checkbox" class="filter-price filtering" data-min="2000000"
                                                data-max="3000000">
                                            Từ 2 triệu - 3 triệu
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="checkbox" class="filter-price filtering" data-min="3000000"
                                                data-max="5000000">
                                            Từ 3 triệu - 5 triệu
                                        </label>
                                    </li>

                                    <li>
                                        <label>
                                            <input type="checkbox" class="filter-price filtering" data-min="5000000"
                                                data-max="7000000">
                                            Từ 5 triệu - 7 triệu
                                        </label>
                                    </li>

                                    <li>
                                        <label>
                                            <input type="checkbox" class="filter-price filtering" data-min="7000000"
                                                data-max="10000000">
                                            Từ 7 triệu - 10 triệu
                                        </label>
                                    </li>

                                    <li>
                                        <label>
                                            <input type="checkbox" class="filter-price filtering" data-min="10000000"
                                                data-max="15000000">
                                            Từ 10 triệu - 15 triệu
                                        </label>
                                    </li>

                                    <li>
                                        <label>
                                            <input type="checkbox" class="filter-price filtering" data-min="15000000"
                                                data-max="20000000">
                                            Từ 15 triệu - 20 triệu
                                        </label>
                                    </li>

                                    <li>
                                        <label>
                                            <input type="checkbox" class="filter-price filtering" data-min="20000000"
                                                data-max="999999999">
                                            Trên 20 triệu
                                        </label>
                                    </li>
                                </ul>

                                @if (isset($filters) && count($filters))
                                    @foreach ($filters as $filter)
                                        @php
                                            $catName = $filter->languages->first()->pivot->name;
                                            $catId = $filter->id;
                                        @endphp
                                        <!-- REGION -->
                                        <h3 class="filter-heading">{{ $catName }}</h3>
                                        @if (isset($filter->attributes))
                                            <ul class="uk-nav filter-list">
                                                @foreach ($filter->attributes as $attribute)
                                                    @php
                                                        $name = $attribute->languages->first()->pivot->name;
                                                        $id = $attribute->id;
                                                        $count = $attribute->product_variants->count();
                                                    @endphp
                                                    <li><label><input value="{{ $id }}"
                                                                class="filtering filterAttribute"
                                                                data-group="{{ $catId }}" type="checkbox">
                                                            {{ $name }} <span
                                                                class="count">({{ $count }})</span></label></li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endforeach
                                @endif

                            </div>

                        </div>
                    </div>
                    <div class="uk-width-large-3-4">
                        <div class="prd-catalogue">
                            <div class="prd-catalogue_description">
                                <h1>{{ $productCatalogue->name }}</h1>
                                <div class="description">
                                    {!! $productCatalogue->description !!}
                                </div>
                            </div>

                            <div class="product-list">
                                @if (!is_null($products))
                                    <ul
                                        class="uk-list uk-clearfix uk-grid uk-grid-small uk-grid-width-1-2 uk-grid-width-small-1-2 uk-grid-width-medium-1-2 uk-grid-width-large-1-3">
                                        @foreach ($products as $keyPost => $valPost)
                                            @php
                                                $title = $valPost->languages->first()->pivot->name;
                                                $canonical = write_url($valPost->languages->first()->pivot->canonical);
                                                $image = $valPost->image;
                                                $href = write_url($valPost->languages->first()->pivot->canonical);
                                                $description = cutnchar(
                                                    strip_tags($valPost->languages->first()->pivot->description),
                                                    100,
                                                );
                                                $price = getPrice($valPost);
                                            @endphp

                                            <li class="mb10">
                                                <div class="product-item">
                                                    <a href="{{ $canonical }}"
                                                        class="image img-scaledown img-zoomin"><img
                                                            src="{{ $image }}" alt="{{ $name }}"></a>
                                                    <div class="info">
                                                    <h3 class="title"><a href="{{ $canonical }}"
                                                                title="{{ $title }}">{{ $title }}</a></h3>
                                                        <div class="price">
                                                            {!! $price['html'] !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="prd-catalogue_description">
                                <div class="description">
                                    {!! $productCatalogue->content !!}
                                </div>
                            </div>
                            <div class="uk-flex uk-flex-center">
                                @include('frontend.component.pagination', ['model' => $products])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
