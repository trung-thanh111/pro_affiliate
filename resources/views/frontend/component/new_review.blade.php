@if(isset($reviewPosts) && count($reviewPosts))
    <section class="panel-new-review py-5">
        <div class="uk-container uk-container-center">
            <div class="panel-head d-flex align-items-center justify-content-between mb-5 flex-wrap gap-2">
                <div class="head-left">
                    <h2 class="section-title-review text-uppercase fw-bold m-0">ĐÁNH GIÁ MỚI</h2>
                    <div class="title-underline-review mt-3"></div>
                    <div class="subtitle text-secondary mt-3">
                        Tổng hợp những nhận xét chân thực và khách quan nhất từ cộng đồng người dùng về các sản phẩm công nghệ mới nhất.
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
                            @foreach($reviewPosts as $post)
                                @include('frontend.component.review_item', ['post' => $post])
                            @endforeach
                        </div>
                        @if(count($reviewPosts) >= 6)
                            <div class="text-center mt-5">
                                <button id="load-more-review" data-page="1" class="btn btn-secondary-review px-5 py-2 fw-bold text-white shadow-sm">
                                    XEM THÊM
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <aside class="review-sidebar">
                            <div class="sidebar-card">
                                <ul class="category-list list-unstyled">
                                    @if(isset($widgets['news']->object))
                                        @foreach($widgets['news']->object as $cat)
                                            @php
                                                $catLang = $cat->languages;
                                            @endphp
                                            <li class="border-bottom py-3">
                                                <a href="{{ write_url($catLang->canonical) }}"
                                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                                    <span class="cat-name">{{ $catLang->name }}</span>
                                                    <i class="bi bi-chevron-right text-secondary small"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif