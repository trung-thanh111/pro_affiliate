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
                @include('frontend.component.review_block', ['posts' => $reviewPosts])
            </div>
            <div id="review-list" class="mt-5">
                {{-- AJAX loaded blocks (1+4) will appear here --}}
            </div>

            <div class="view-more-footer text-center mt-5">
                <button type="button" id="load-more-review" data-page="1" class="view-all-standard border-primary text-primary px-5 py-2 fw-bold text-uppercase">
                    Xem thêm đánh giá <i class="bi bi-chevron-down ms-2"></i>
                </button>
            </div>
        </div>
    </section>
@endif
