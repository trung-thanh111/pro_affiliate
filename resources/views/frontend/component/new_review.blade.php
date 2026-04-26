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
                    @php
                        $featured = $reviewPosts->first();
                        $sideItems = $reviewPosts->slice(1, 4);

                        $fLang = $featured->languages->first();
                        $fName = $fLang->pivot->name;
                        $fCanonical = write_url($fLang->pivot->canonical);
                        $fTime = date('d/m/Y', strtotime($featured->created_at));
                        $fDesc = $fLang->pivot->description;
                    @endphp

                    <div class="col-lg-9">
                        <div class="new-review-featured">
                            <a href="{{ $fCanonical }}" class="text-decoration-none text-dark">
                                <div class="featured-img-wrapper mb-3">
                                    <img src="{{ $featured->image }}" alt="{{ $fName }}">
                                </div>
                                <h3 class="featured-title">{{ $fName }}</h3>
                                <div class="featured-meta mb-3">
                                    <span class="date">{{ $fTime }}</span>
                                </div>
                                <div class="featured-desc">
                                    {{ $fDesc }}
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="new-review-side-list">
                            @foreach ($sideItems as $item)
                                @php
                                    $lang = $item->languages->first();
                                    $name = $lang->pivot->name;
                                    $canonical = write_url($lang->pivot->canonical);
                                @endphp
                                <div class="side-review-item mb-4">
                                    <a href="{{ $canonical }}" class="text-decoration-none text-dark">
                                        <div class="side-img-wrapper mb-2">
                                            <img src="{{ $item->image }}" alt="{{ $name }}">
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
            </div>
        </div>
    </section>
@endif
