@foreach($posts as $key => $val)
    @php
        $lang = $val->languages->first();
        $title = $lang->pivot->name ?? $val->name ?? '';
        $image = $val->image;
        $href = write_url($lang->pivot->canonical ?? '');
        $description = cutnchar(strip_tags($lang->pivot->description ?? ''), 150);
        $date = convertDateTime($val->created_at, 'H:i d/m/Y');
        
        $product = $val->product ?? null;
        $affiliateUrl = (!empty($product->link)) 
            ? $product->link 
            : ($fallbackAffiliateUrl ?? '');
    @endphp


    <div class="uk-width-1-1 uk-width-medium-1-2 uk-width-large-1-3 mb-4">
        <div class="news-item-premium h-100 bg-white rounded-4 overflow-hidden shadow-sm transition-all border border-light js-news-item" 
             data-affiliate="{{ $affiliateUrl }}"
             data-href="{{ $href }}">
            <a href="{{ $href }}" class="d-block ratio ratio-16x9 overflow-hidden bg-light js-news-link">
                <img src="{{ $image }}" alt="{{ $title }}" class="img-fluid object-fit-cover w-100 h-100 transition-transform duration-500">
            </a>
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex align-items-center mb-2">
                    <span class="text-danger fw-bold text-uppercase x-small me-3"><i class="bi bi-lightning-fill me-1"></i> Tin nhanh</span>
                    <span class="text-muted x-small me-3"><i class="bi bi-clock me-1"></i> {{ $date }}</span>
                    <span class="text-muted x-small"><i class="bi bi-eye me-1"></i> {{ $val->viewed ?? 0 }}</span>
                </div>
                <h3 class="h5 fw-bold mb-3">
                    <a href="{{ $href }}" class="text-dark text-decoration-none hover-text-primary line-clamp-2 js-news-link">{{ $title }}</a>
                </h3>
                <div class="text-muted small line-clamp-3 mb-3">
                    {!! $description !!}
                </div>
                <div class="mt-auto text-end">
                    <a href="{{ $href }}" class="btn-readmore text-primary fw-bold small text-decoration-none text-uppercase js-news-link">
                        Xem chi tiết <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
