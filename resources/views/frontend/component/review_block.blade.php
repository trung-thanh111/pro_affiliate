@if (isset($posts) && count($posts))
    @php
        $featured = $posts->first();
        $sideItems = $posts->slice(1, 2);
        $listItems = $posts->slice(3, 3);

        $fLang = $featured->languages->first();
        $fName = $fLang->pivot->name ?? $featured->name ?? '';
        $fCanonical = write_url($fLang->pivot->canonical ?? '');
        $fTime = convertDateTime($featured->created_at, 'H:i d/m/Y');
        $fDesc = strip_tags($fLang->pivot->description ?? '');
    @endphp

    <div class="review-block-container">
        {{-- Top Part: 1 Big + 2 Small --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                @php
                    $product = $featured->product ?? null;
                    $fAffiliateUrl = (!empty($product->link)) 
                        ? $product->link 
                        : ($fallbackAffiliateUrl ?? '');
                @endphp

                <div class="new-review-featured js-news-item" data-affiliate="{{ $fAffiliateUrl }}" data-href="{{ $fCanonical }}">

                    <a href="{{ $fCanonical }}" class="text-decoration-none text-dark">
                        <div class="featured-img-wrapper mb-3">
                            <img src="{{ $featured->image }}" alt="{{ $fName }}" class="rounded-3">
                        </div>
                        <h3 class="featured-title">{{ $fName }}</h3>
                        <div class="featured-meta mb-2">
                            <span class="date">{{ $fTime }}</span>
                        </div>
                        <div class="featured-desc">
                            {{ Str::limit($fDesc, 200) }}
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="new-review-side-list">
                    @foreach ($sideItems as $item)
                        @php
                            $lang = $item->languages->first();
                            $name = $lang->pivot->name ?? $item->name ?? '';
                            $canonical = write_url($lang->pivot->canonical ?? '');

                            $product = $item->product ?? null;
                            $affiliateUrl = (!empty($product->link)) 
                                ? $product->link 
                                : ($fallbackAffiliateUrl ?? '');
                        @endphp

                        <div class="side-review-item mb-4 js-news-item" data-affiliate="{{ $affiliateUrl }}" data-href="{{ $canonical }}">

                            <a href="{{ $canonical }}" class="text-decoration-none text-dark">
                                <div class="side-img-wrapper mb-2">
                                    <img src="{{ $item->image }}" alt="{{ $name }}" class="rounded-3">
                                </div>
                                <div class="side-content">
                                    <h4 class="side-title">{{ $name }}</h4>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bottom Part: List of items --}}
        <div class="review-list-horizontal">
            <div id="load-more-target-review">
                @include('frontend.component.review_item_horizontal', ['posts' => $listItems])
            </div>
        </div>

        @if(count($posts) >= 6)
            <div class="text-center mt-5">
                <button class="btn btn-outline-primary btn-load-more-review rounded-pill px-5 py-2 fw-bold" 
                        style="border-width: 2px; transition: all 0.3s;"
                        data-page="2">
                    XEM THÊM <i class="bi bi-chevron-down ms-2"></i>
                </button>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('.btn-load-more-review').on('click', function() {
                let btn = $(this);
                let page = btn.data('page');
                
                btn.html('<span class="spinner-border spinner-border-sm me-2"></span> ĐANG TẢI...');
                
                $.ajax({
                    url: '/ajax/post/load-review',
                    type: 'GET',
                    data: {
                        page: page
                    },
                    success: function(res) {
                        if (res.html != '') {
                            $('#load-more-target-review').append(res.html);
                            btn.data('page', page + 1);
                            btn.html('XEM THÊM <i class="bi bi-chevron-down ms-2"></i>');
                            
                            if (!res.hasMore) {
                                btn.parent().fadeOut();
                            }
                        } else {
                            btn.parent().fadeOut();
                        }
                    },
                    error: function() {
                        btn.html('XEM THÊM <i class="bi bi-chevron-down ms-2"></i>');
                    }
                });
            });
        });
    </script>
    @endpush
@endif
