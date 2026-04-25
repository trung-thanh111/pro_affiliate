@if (isset($reviewPosts) && count($reviewPosts))
    <section class="panel-new-review py-5">
        <div class="uk-container uk-container-center">
            <div class="panel-head d-flex align-items-center justify-content-between mb-5 flex-wrap gap-2">
                <div class="head-left">
                    <h2 class="section-title-review text-uppercase fw-bold m-0">ĐÁNH GIÁ MỚI</h2>
                    <div class="title-underline-review mt-3"></div>
                    <div class="subtitle text-secondary mt-3">
                        Tổng hợp những nhận xét chân thực và khách quan nhất từ cộng đồng người dùng về các sản phẩm
                        công nghệ mới nhất.
                    </div>
                </div>
                <div class="head-right">
                    <a href="#" class="view-all-review text-decoration-none fw-bold">Xem tất cả</a>
                </div>
            </div>

            <div class="panel-body mt-4">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div id="review-list">
                            @foreach ($reviewPosts as $post)
                                @include('frontend.component.review_item', ['post' => $post])
                            @endforeach
                        </div>
                        @if (count($reviewPosts) >= 6)
                            <div class="text-center mt-5">
                                <button id="load-more-review" data-page="1"
                                    class="btn btn-secondary-review px-4 py-2 text-white">
                                    XEM THÊM
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <aside class="review-sidebar">
                            <div class="sidebar-card">
                                <h3 class="sidebar-title text-uppercase fw-bold mb-4">Đánh giá nổi bật</h3>
                                <div class="featured-reviews-list">
                                    @if (isset($featuredReviews))
                                        @foreach ($featuredReviews as $index => $post)
                                            @php
                                                $lang = $post->languages->first();
                                                $name = $lang->pivot->name;
                                                $canonical = write_url($lang->pivot->canonical);
                                                $time = date('d-m-Y', strtotime($post->created_at));
                                            @endphp
                                            @if ($index == 0)
                                                <div class="featured-item-large mb-4 pb-4 border-bottom">
                                                    <a href="{{ $canonical }}"
                                                        class="text-decoration-none text-dark">
                                                        <div class="image-wrapper mb-3 overflow-hidden rounded">
                                                            <img src="{{ $post->image }}" class="img-fluid transition"
                                                                alt="{{ $name }}">
                                                        </div>
                                                        <h4 class="title fw-bold mb-2">{{ $name }}</h4>
                                                        <div class="time text-secondary small">
                                                            <i class="bi bi-clock me-1"></i> {{ $time }}
                                                        </div>
                                                    </a>
                                                </div>
                                            @else
                                                <div
                                                    class="featured-item-small mb-3 pb-3 @if ($index < count($featuredReviews) - 1) border-bottom @endif">
                                                    <a href="{{ $canonical }}"
                                                        class="text-decoration-none text-dark">
                                                        <h5 class="title fw-normal mb-1">{{ $name }}</h5>
                                                        <div class="time text-secondary x-small">
                                                            <i class="bi bi-clock me-1"></i> {{ $time }}
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
