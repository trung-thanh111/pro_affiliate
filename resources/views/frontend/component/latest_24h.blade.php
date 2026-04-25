@if (isset($latestPosts) && count($latestPosts))
    <section class="panel-latest-24h py-5">
        <div class="uk-container uk-container-center">
            <div class="panel-head d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div class="head-left">
                    <h2 class="section-title-24h text-uppercase fw-bold m-0">MỚI NHẤT TRONG 24H</h2>
                    <div class="title-underline-24h mt-3"></div>
                    <div class="subtitle text-secondary mt-3">
                        Cập nhật siêu tốc những tin tức, xu hướng và sự kiện nổi bật vừa diễn ra trong vòng 24 giờ qua.
                    </div>
                </div>
                <div class="head-right">
                    <a href="#" class="view-all-24h text-decoration-none fw-bold">Xem tất cả</a>
                </div>
            </div>
            <div class="panel-body mt-5">
                <div class="row g-4">
                    @foreach ($latestPosts->take(6) as $post)
                        @php
                            $lang = $post->languages->first();
                            $name = $lang->pivot->name;
                            $desc = $lang->pivot->description;
                            $canonical = write_url($lang->pivot->canonical);
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="news-item-24h">
                                <a href="{{ $canonical }}" class="d-flex gap-3 text-decoration-none">
                                    <div class="news-img-24h flex-shrink-0">
                                        <img src="{{ $post->image }}" alt="{{ $name }}"
                                            class="rounded-3 object-fit-cover">
                                    </div>
                                    <div class="news-content-24h">
                                        <h4 class="news-title-24h mb-1">{{ Str::limit($name, 60) }}</h4>
                                        <div class="news-desc-24h text-dark small">
                                            {{ Str::limit(strip_tags($desc), 120) }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
