@foreach ($posts as $item)
    @php
        $lang = $item->languages->first();
        $name = $lang->pivot->name;
        $desc = strip_tags($lang->pivot->description);
        $canonical = write_url($lang->pivot->canonical);
        $time = date('d/m/Y', strtotime($item->created_at));

        $product = $item->product ?? null;
        $affiliateUrl = (!empty($product->link)) 
            ? $product->link 
            : ($fallbackAffiliateUrl ?? '');
    @endphp

    <div class="horizontal-review-item py-4 border-top js-news-item" data-affiliate="{{ $affiliateUrl }}" data-href="{{ $canonical }}">

        <a href="{{ $canonical }}" class="row g-4 text-decoration-none text-dark">
            <div class="col-md-4 col-4">
                <div class="item-img-wrapper">
                    <img src="{{ $item->image }}" alt="{{ $name }}" class="rounded-3 w-100 object-fit-cover" style="height: 180px;">
                </div>
            </div>
            <div class="col-md-8 col-8">
                <div class="item-content">
                    <h3 class="item-title mb-2">{{ $name }}</h3>
                    <div class="item-meta mb-2">
                        <span class="date text-secondary small">{{ $time }}</span>
                    </div>
                    <div class="item-desc text-secondary d-none d-md-block">
                        {{ Str::limit($desc, 250) }}
                    </div>
                </div>
            </div>
        </a>
    </div>
@endforeach
