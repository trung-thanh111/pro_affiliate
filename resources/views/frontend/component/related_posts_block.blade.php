@if (isset($relatedPosts) && count($relatedPosts))
    @php
        $featured = $relatedPosts->first();
        $sideItems = $relatedPosts->slice(1, 2);
        $listItems = $relatedPosts->slice(3, 3);

        $fLang = $featured->languages->first();
        $fName = $fLang->pivot->name;
        $fCanonical = write_url($fLang->pivot->canonical);
        $fTime = date('d/m/Y', strtotime($featured->created_at));
        $fDesc = strip_tags($fLang->pivot->description);
        $fRedirectUrl = get_post_affiliate_url($featured);
    @endphp

    <div class="related-posts-section p-3 bg-white">
        <div class="panel-head mb-4">
            <h2 class="heading-standard text-uppercase fw-bold m-0" style="font-size: 1.5rem;">
                <span>BÀI VIẾT LIÊN QUAN</span>
            </h2>
        </div>

        <div class="review-block-container" id="related-posts-container">
            {{-- Top Part: 1 Big + 2 Small --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="new-review-featured h-100 bg-white rounded-3 overflow-hidden" data-redirect="{{ $fRedirectUrl }}">
                        <a href="{{ $fCanonical }}" class="text-decoration-none text-dark d-block h-100 post-link-redirect" data-redirect="{{ $fRedirectUrl }}">
                            <div class="featured-img-wrapper" style="height: 380px; overflow: hidden;">
                                <img src="{{ $featured->image }}" alt="{{ $fName }}"
                                    class="w-100 h-100 object-fit-cover transition-transform">
                            </div>
                            <div class="featured-content p-3">
                                <h3 class="featured-title fw-bold mb-2"
                                    style="font-size: 1.4rem; color: #1a1a1a; line-height: 1.4;">{{ $fName }}
                                </h3>
                                <div class="featured-meta mb-2">
                                    <span class="date text-muted small"><i class="bi bi-calendar3 me-1"></i>
                                        {{ $fTime }}</span>
                                </div>
                                <div class="featured-desc text-muted small" style="line-height: 1.6;">
                                    {{ Str::limit($fDesc, 180) }}
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="new-review-side-list d-flex flex-column gap-4 h-100">
                        @foreach ($sideItems as $item)
                            @php
                                $lang = $item->languages->first();
                                $name = $lang->pivot->name;
                                $canonical = write_url($lang->pivot->canonical);
                                $itemRedirectUrl = get_post_affiliate_url($item);
                            @endphp
                            <div class="side-review-item bg-white rounded-3 overflow-hidden flex-grow-1" data-redirect="{{ $itemRedirectUrl }}">
                                <a href="{{ $canonical }}"
                                    class="text-decoration-none text-dark d-flex flex-column h-100 post-link-redirect" data-redirect="{{ $itemRedirectUrl }}">
                                    <div class="side-img-wrapper" style="height: 140px; overflow: hidden;">
                                        <img src="{{ $item->image }}" alt="{{ $name }}"
                                            class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="side-content p-3">
                                        <h4 class="side-title fw-bold mb-2"
                                            style="font-size: 0.95rem; color: #1a1a1a; line-height: 1.4;">
                                            {{ $name }}</h4>
                                        <div class="side-desc text-muted small"
                                            style="font-size: 0.8rem; line-height: 1.5;">
                                            {{ Str::limit(strip_tags($item->languages->first()->pivot->description), 80) }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Bottom Part: List of items --}}
            <div class="review-list-horizontal">
                <div id="related-posts-load-target">
                    @include('frontend.component.review_item_horizontal', ['posts' => $listItems])
                </div>
            </div>

            @if (count($relatedPosts) >= 6)
                <div class="text-center mt-5">
                    <button class="btn btn-primary btn-load-more-related rounded-pill px-5 py-2 fw-bold shadow-sm"
                        style="transition: all 0.3s;" data-page="2" data-catalogue-id="{{ $productCatalogue->id }}">
                        XEM THÊM <i class="bi bi-chevron-down ms-2"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.btn-load-more-related').on('click', function() {
                    let btn = $(this);
                    let page = btn.data('page');
                    let catalogueId = btn.data('catalogue-id');

                    btn.html('<span class="spinner-border spinner-border-sm me-2"></span> ĐANG TẢI...');

                    $.ajax({
                        url: '/ajax/post/load-related',
                        type: 'GET',
                        data: {
                            page: page,
                            catalogueId: catalogueId
                        },
                        success: function(res) {
                            if (res.html != '') {
                                $('#related-posts-load-target').append(res.html);
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
