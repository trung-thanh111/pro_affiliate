@if (isset($reviewPosts) && count($reviewPosts))
    <section class="panel-new-review py-5">
        <div class="uk-container uk-container-center">
            <div class="panel-head d-flex align-items-center justify-content-between mb-5 flex-wrap gap-2">
                <div class="head-left">
                    <h2 class="heading-standard text-uppercase fw-bold m-0">
                        <span>ĐÁNH GIÁ MỚI</span>
                    </h2>
                </div>
                <div class="head-right">
                    <a href="{{ write_url('danh-gia') }}" class="view-all-standard text-decoration-none fw-bold">
                        Xem tất cả <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
            </div>

            <div class="panel-body">
                <div class="row g-4">
                    <div class="col-lg-9">
                        @include('frontend.component.review_block', ['posts' => $reviewPosts->take(6)])
                        
                        <div id="review-list" class="mt-4">
                            {{-- AJAX loaded blocks will appear here --}}
                        </div>

                        <div class="view-more-footer text-center mt-5">
                            <button type="button" id="load-more-review" data-page="1" class="view-all-standard text-primary px-5 py-2 fw-bold text-uppercase">
                                Xem thêm <i class="bi bi-chevron-down ms-2"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="review-sidebar">
                            <div class="ads-banner mb-4">
                                <img src="https://placehold.co/300x600?text=Quảng+Cáo" alt="Ads" class="w-100 rounded-3">
                            </div>
                            <div class="ads-banner">
                                <img src="https://placehold.co/300x250?text=Quảng+Cáo" alt="Ads" class="w-100 rounded-3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
