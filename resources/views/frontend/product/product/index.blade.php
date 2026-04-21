@php
    $prd_title = $product->languages->first()->pivot->name ?? $product->name;
    $prd_code = $product->code;

    $albumSource = is_array($product->album) ? $product->album : json_decode($product->album ?? '[]', true);
    $list_image = array_values(array_filter(is_array($albumSource) ? $albumSource : []));

    if (!empty($product->image)) {
        array_unshift($list_image, $product->image);
    }

    $list_image = array_values(array_unique($list_image));
    $prd_href = write_url($product->canonical ?? $product->languages->first()->pivot->canonical);
    $price = getPrice($product);
    $stockQuantity = (int) ($product->stock ?? 0);
    $sold = $product->sold ?? 0;

    if ($sold >= 1000) {
        $sold_text = number_format($sold / 1000, 1, ',', '.') . 'k';
    } else {
        $sold_text = $sold;
    }
@endphp

@extends('frontend.homepage.layout')

@section('content')
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <div class="shopee-product-detail">
        <div class="uk-container uk-container-center">
            <x-breadcrumb :breadcrumb="$breadcrumb" />

            <div class="product-main-card">
                <div class="uk-grid uk-grid-collapse">
                    <!-- Left: Gallery -->
                    <div class="uk-width-large-2-5">
                        <div class="gallery-section">
                            <div class="big-image-container">
                                <a data-fancybox="gallery" href="{{ image($list_image[0] ?? '') }}" id="main-product-link">
                                    <img src="{{ image($list_image[0] ?? '') }}" alt="{{ $prd_title }}"
                                        id="main-product-img">
                                </a>
                            </div>
                            <div class="thumb-list">
                                @foreach($list_image as $key => $img)
                                    <div class="thumb-item {{ $key == 0 ? 'active' : '' }}"
                                        onclick="changeImage('{{ image($img) }}', this)">
                                        <img src="{{ image($img) }}" alt="{{ $prd_title }}">
                                    </div>
                                    <!-- Hidden links for fancybox to recognize all images in gallery -->
                                    @if($key > 0)
                                        <a data-fancybox="gallery" href="{{ image($img) }}" class="uk-hidden"></a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right: Info -->
                    <div class="uk-width-large-3-5">
                        <div class="info-section">
                            <h1 class="product-title">{{ $prd_title }}</h1>

                            <div class="meta-stats">
                                <div class="sold-count">
                                    <span style="color: #222">{{ $sold_text }}</span> Đã bán
                                </div>
                                @if(!empty($product->source))
                                    <div class="source-tag">
                                        <i class="fa fa-shopping-bag"></i>
                                        <span>Nguồn: {{ $product->source }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="price-section">
                                @if(isset($product->promotions) && is_object($product->promotions))
                                    <span class="price-current">{{ convert_price($price['priceSale'], true) }}₫</span>
                                    <span class="price-old">{{ convert_price($price['price'], true) }}₫</span>
                                    <span class="discount-tag">-{{ $price['percent'] }}%</span>
                                @else
                                    <span class="price-current">{{ convert_price($price['price'], true) }}₫</span>
                                @endif
                            </div>

                            <div class="detail-row">
                                <div class="label">Mã sản phẩm</div>
                                <div class="value">{{ $prd_code }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="label">Số lượng</div>
                                <div class="value">
                                    <div class="qty-selector">
                                        <button type="button" onclick="changeQty(-1)">-</button>
                                        <input type="text" value="1" id="prd-qty">
                                        <button type="button" onclick="changeQty(1)">+</button>
                                    </div>
                                    <span style="color: #757575; margin-left: 15px">{{ $stockQuantity }} sản phẩm có
                                        sẵn</span>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <a href="{{ $prd_href }}?redirect=1" class="btn-add-cart">
                                    <i class="fa fa-cart-plus" style="font-size: 20px"></i>
                                    <span>Thêm vào giỏ hàng</span>
                                </a>
                                <a href="{{ $prd_href }}?redirect=1" class="btn-buy-now">Mua ngay</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-content-card">
                <div class="section-title">MÔ TẢ SẢN PHẨM</div>
                <div class="content-body">
                    {!! $product->languages->first()->pivot->content ?? $product->content !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Fancybox JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        // Initialize Fancybox
        Fancybox.bind("[data-fancybox]", {
            // Your options go here
        });

        function changeImage(src, el) {
            document.getElementById('main-product-img').src = src;
            document.getElementById('main-product-link').href = src;
            document.querySelectorAll('.thumb-item').forEach(item => item.classList.remove('active'));
            el.classList.add('active');
        }

        function changeQty(delta) {
            let input = document.getElementById('prd-qty');
            let val = parseInt(input.value) + delta;
            if (val < 1) val = 1;
            input.value = val;
        }
    </script>
@endsection