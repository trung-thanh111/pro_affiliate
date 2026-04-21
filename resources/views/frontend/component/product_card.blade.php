@php
    $name = $product->name ?? $product->languages->first()->pivot->name;
    $canonical = write_url($product->canonical ?? $product->languages->first()->pivot->canonical);
    $image = thumb(image($product->image), 600, 600);
    $price = getPrice($product);
    $sold = $product->sold ?? 0;
    // Format sold number
    if ($sold >= 1000) {
        $sold_text = number_format($sold / 1000, 1, ',', '.') . 'k';
    } else {
        $sold_text = $sold;
    }
@endphp

<div class="modern-product-card">
    <div class="image-box">
        <a href="{{ $canonical }}" title="{{ $name }}">
            <img src="{{ $image }}" alt="{{ $name }}">
        </a>
        @if($price['percent'] > 0)
            <div class="badge-discount">-{{ $price['percent'] }}%</div>
        @endif
    </div>
    <div class="info-box">
        <h3 class="title">
            <a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a>
        </h3>
        <div class="price-action">
            <div class="price-group">
                <span class="price-sale">{{ ($price['priceSale'] > 0) ? convert_price($price['priceSale'], true) : convert_price($price['price'], true) }}₫</span>
                @if($price['priceSale'] > 0)
                    <span class="price-old">{{ convert_price($price['price'], true) }}₫</span>
                @endif
            </div>
            <a href="{{ $canonical }}?redirect=1" class="btn-cart" title="Mua ngay">
                <i class="fa fa-shopping-cart"></i>
            </a>
        </div>

        <div class="footer-meta">
            @if(!empty($product->source))
                <span class="status" style="color: #ee4d2d"><i class="fa fa-shopping-bag" style="font-size: 10px; margin-right: 3px;"></i>{{ $product->source }}</span>
            @else
                <span class="status">Còn hàng</span>
            @endif
            <span class="sold">Đã bán: {{ $sold_text }}</span>
        </div>
    </div>
</div>
