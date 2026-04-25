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

<a href="{{ $canonical }}?redirect=1" target="_blank" title="{{ $name }}" class="modern-product-card-link text-decoration-none d-block">
    <div class="modern-product-card">
        <div class="image-box">
            <img src="{{ $image }}" alt="{{ $name }}">
            @if($price['percent'] > 0)
                <div class="badge-discount">-{{ $price['percent'] }}%</div>
            @endif
        </div>
        <div class="info-box">
            <h3 class="title">
                {{ $name }}
            </h3>
            <div class="price-action">
                <div class="price-group">
                    <span class="price-sale">{{ ($price['priceSale'] > 0) ? convert_price($price['priceSale'], true) : convert_price($price['price'], true) }}₫</span>
                    @if($price['priceSale'] > 0)
                        <span class="price-old">{{ convert_price($price['price'], true) }}₫</span>
                    @endif
                </div>
                <div class="btn-cart" title="Mua ngay">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>

            <div class="footer-meta">
                @if(!empty($product->source))
                    <span class="status" style="color: #ee4d2d; font-weight: 600;"><i class="fa fa-shopping-bag" style="font-size: 10px; margin-right: 3px;"></i>{{ $product->source }}</span>
                @else
                    <span class="status" style="color: #4cd137; font-weight: 600;">Còn hàng</span>
                @endif
                <span class="sold">Đã bán: {{ $sold_text }}</span>
            </div>
        </div>
    </div>
</a>
