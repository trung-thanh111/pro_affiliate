@extends('frontend.homepage.layout')

@section('content')
    <div id="prd-catalogue" class="page-body py-5 bg-light">
        <div class="uk-container uk-container-center">
            <x-breadcrumb :breadcrumb="$breadcrumb" />

            <div class="row g-4 mt-2">
                <!-- Sidebar -->
                <aside class="col-lg-3">
                    <div class="sidebar-wrapper sticky-top" style="top: 100px; z-index: 10;">
                        <!-- Category Tree -->
                        <div class="filter-card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                            <h5 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-grid-3x3-gap me-2 text-primary"></i> Danh Mục
                            </h5>
                            <div class="category-tree">
                                @include('frontend.product.catalogue.component.product-catalogue-tree', [
                                    'items' => $productCatalogues,
                                ])
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="filter-card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                            <h5 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-tag me-2 text-primary"></i> Khoảng Giá
                            </h5>
                            <div class="price-filters mt-3">
                                @php
                                    $priceRanges = [
                                        ['min' => 0, 'max' => 500000, 'label' => 'Dưới 500k'],
                                        ['min' => 500000, 'max' => 1000000, 'label' => '500k - 1tr'],
                                        ['min' => 1000000, 'max' => 2000000, 'label' => '1tr - 2tr'],
                                        ['min' => 2000000, 'max' => 5000000, 'label' => '2tr - 5tr'],
                                        ['min' => 5000000, 'max' => 10000000, 'label' => '5tr - 10tr'],
                                        ['min' => 10000000, 'max' => 0, 'label' => 'Trên 10tr'],
                                    ];
                                @endphp
                                @foreach ($priceRanges as $range)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input filter-price filtering" type="checkbox"
                                            value="" id="price-{{ $loop->index }}" data-min="{{ $range['min'] }}"
                                            data-max="{{ $range['max'] }}">
                                        <label class="form-check-label" for="price-{{ $loop->index }}">
                                            {{ $range['label'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if (isset($filters) && count($filters))
                            @foreach ($filters as $filter)
                                @php
                                    $catName = $filter->languages->first()->pivot->name;
                                    $catId = $filter->id;
                                @endphp
                                <div class="filter-card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                                    <h5 class="fw-bold mb-3">{{ $catName }}</h5>
                                    @if (isset($filter->attributes))
                                        <div class="attribute-filters">
                                            @foreach ($filter->attributes as $attribute)
                                                @php
                                                    $name = $attribute->languages->first()->pivot->name;
                                                    $id = $attribute->id;
                                                    $count = $attribute->product_variants->count();
                                                @endphp
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input filtering filterAttribute"
                                                        type="checkbox" value="{{ $id }}"
                                                        id="attr-{{ $id }}" data-group="{{ $catId }}">
                                                    <label class="form-check-label d-flex justify-content-between"
                                                        for="attr-{{ $id }}">
                                                        <span>{{ $name }}</span>
                                                        <span class="text-muted opacity-50">({{ $count }})</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </aside>

                <!-- Product List -->
                <main class="col-lg-9">
                    <div class="prd-catalogue-header mb-4 bg-white p-4 rounded-4 shadow-sm">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="heading-promotion" style="margin:0">{{ $productCatalogue->name }}</h1>
                                <div class="description text-muted small mt-3">
                                    {!! $productCatalogue->description !!}
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="d-inline-flex align-items-center bg-light p-2 rounded-pill border">
                                    <span class="small text-muted me-2 ms-2"><i class="bi bi-sort-down me-1"></i> Sắp
                                        xếp:</span>
                                    <select name="sortType"
                                        class="form-select form-select-sm border-0 shadow-none bg-transparent filtering"
                                        style="width: auto; cursor: pointer;">
                                        <option value="id-desc">Mới nhất</option>
                                        <option value="price-asc">Giá thấp đến cao</option>
                                        <option value="price-desc">Giá cao đến thấp</option>
                                        <option value="popular">Bán chạy nhất</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="product-grid mt-4">
                        <input type="hidden" class="product_catalogue_id" value="{{ $productCatalogue->id }}">
                        <div id="product-results-grid">
                            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                                @if ($products->count() > 0)
                                    @foreach ($products as $product)
                                        <div class="col">
                                            @include('frontend.component.product_card', [
                                                'product' => $product,
                                            ])
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm">
                                        <img src="/backend/img/no-product.png" alt="No product" class="img-fluid mb-3"
                                            style="max-width: 150px; opacity: 0.5;">
                                        <p class="text-muted">Không tìm thấy sản phẩm nào trong danh mục này.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 d-flex justify-content-center">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>

                    @if (!empty($productCatalogue->content))
                        <div class="catalogue-content mt-5 p-4 bg-white rounded-4 shadow-sm border-0">
                            <div class="content-body small text-muted lh-lg">
                                {!! $productCatalogue->content !!}
                            </div>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                console.log('--- EMERGENCY FILTER READY ---');

                function updateUI(res) {
                    console.log('Updating UI with', res.countProduct, 'products');

                    // 1. Target the Grid
                    let $container = $('#product-results-grid');
                    let $row = $container.find('.row');

                    if ($row.length) {
                        $row.html(res.data);
                    } else {
                        $container.html('<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">' + res
                            .data + '</div>');
                    }

                    // 2. Update Count
                    if (res.countProduct !== undefined) {
                        $('.caption strong').html(res.countProduct + ' sản phẩm');
                    }

                    $container.css('opacity', '1');
                }

                function triggerFilter() {
                    let filterOption = {
                        'attributes': {},
                        'price': {
                            'price_min': $('.price_min').val() || '',
                            'price_max': $('.price_max').val() || ''
                        },
                        'productCatalogueId': $('.product_catalogue_id').val(),
                        'sortType': $('select[name=sortType]').val(),
                    };

                    $('.filterAttribute:checked').each(function() {
                        let group = $(this).attr('data-group');
                        if (!filterOption.attributes.hasOwnProperty(group)) {
                            filterOption.attributes[group] = [];
                        }
                        filterOption.attributes[group].push($(this).val());
                    });

                    $('.filter-price:checked').each(function() {
                        let min = $(this).attr('data-min');
                        let max = $(this).attr('data-max');
                        if (filterOption.price.price_min == '' || parseInt(min) < parseInt(filterOption
                                .price.price_min)) {
                            filterOption.price.price_min = min;
                        }
                        if (filterOption.price.price_max == '' || parseInt(max) > parseInt(filterOption
                                .price.price_max)) {
                            filterOption.price.price_max = max;
                        }
                    });

                    console.log('Executing AJAX Request...', filterOption);

                    $.ajax({
                        url: '/ajax/product/filter',
                        type: 'GET',
                        data: filterOption,
                        dataType: 'json',
                        beforeSend: function() {
                            $('#product-results-grid').css('opacity', '0.5');
                        },
                        success: function(res) {
                            updateUI(res);
                        },
                        error: function(xhr) {
                            $('#product-results-grid').css('opacity', '1');
                            console.error('FAIL:', xhr.responseText);
                        }
                    });
                }

                // Bind to all filtering inputs
                $(document).on('change', '.filtering, .filterAttribute, .filter-price, select[name=sortType]',
                    function() {
                        triggerFilter();
                    });
            });
        })(jQuery);
    </script>
@endpush

@push('css')
    <style>
        .filter-card .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .category-tree ul {
            list-style: none;
            padding-left: 0;
        }

        .category-tree ul ul {
            padding-left: 1rem;
            margin-top: 0.5rem;
        }

        .category-tree label {
            cursor: pointer;
            transition: all 0.2s;
        }

        .category-tree label:hover {
            color: var(--primary-color);
        }

        .prd-catalogue-header .description {
            max-width: 600px;
        }

        .product-grid .col {
            transition: transform 0.3s ease;
        }

        .product-grid .col:hover {
            z-index: 1;
        }

        /* Standardize Filter Styles */
        .filter-card .form-check-label {
            font-size: 1.15rem !important;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            transition: all 0.2s ease;
            padding-left: 10px;
            display: flex;
            align-items: center;
        }

        .filter-card .form-check {
            margin-bottom: 12px !important;
            display: flex;
            align-items: center;
        }

        .filter-card .form-check:hover .form-check-label {
            color: var(--primary-color);
            padding-left: 14px;
        }

        .filter-card .form-check-input {
            width: 1.5em;
            height: 1.5em;
            margin-top: 0;
            cursor: pointer;
            border: 2px solid #ccc;
            flex-shrink: 0;
        }

        .filter-card .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .filter-card h5 {
            font-size: 1.35rem;
            font-weight: 800;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 12px;
            margin-bottom: 25px !important;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-card {
            padding: 30px !important;
        }

        /* Override Small Class in Filters */
        .filter-card .small {
            font-size: 1.15rem !important;
        }
    </style>
@endpush
