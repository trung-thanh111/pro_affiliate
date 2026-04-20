@if(isset($widgets['news']->object))
    @foreach($widgets['news']->object as $key => $cat)
        @php
            $catName = $cat->languages->name;
            $catCanonical = write_url($cat->languages->canonical);
            $posts = $cat->posts ?? [];
        @endphp
        @if(count($posts))
            <section class="panel-review-handbook mt50 mb50">
                <div class="uk-container uk-container-center">
                    <div class="panel-head d-flex align-items-center justify-content-between mb30 flex-wrap gap-2">
                        <div class="head-left">
                            <h2 class="heading-handbook">
                                <span>CẨM NANG REVIEW</span>
                            </h2>
                            <div class="heading-line"></div>
                            <div class="subtitle mt10">
                                Khám phá những bí quyết và kinh nghiệm thực tế để chọn lựa sản phẩm hoàn hảo nhất cho nhu cầu của bạn.
                            </div>
                        </div>
                        <div class="head-right">
                            <a href="{{ $catCanonical }}" class="view-all-handbook">Xem tất cả</a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="uk-grid uk-grid-medium">
                            {{-- Large Post --}}
                            @if(isset($posts[0]))
                                @php
                                    $p1 = $posts[0];
                                    $lang1 = $p1->languages->first();
                                    $n1 = $lang1->pivot->name;
                                    $d1 = $lang1->pivot->description;
                                    $i1 = $p1->image;
                                    $c1 = write_url($lang1->pivot->canonical);
                                @endphp
                                <div class="uk-width-large-2-5">
                                    <div class="handbook-item large">
                                        <a href="{{ $c1 }}" class="image-wrap">
                                            <img src="{{ $i1 }}" alt="{{ $n1 }}">
                                            <div class="overlay"></div>
                                            <div class="info-top">
                                                <div class="tag">TIN MỚI NHẤT</div>
                                            </div>
                                            <div class="info-middle">
                                                <h3 class="title">{{ $n1 }}</h3>
                                                <div class="description">{{ Str::limit(strip_tags($d1), 150) }}</div>
                                            </div>
                                            <div class="info-bottom-right">
                                                <div class="readmore">Xem bài viết <i class="fa fa-arrow-right"></i></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <div class="uk-width-large-3-5">
                                <div class="uk-grid uk-grid-medium uk-grid-width-large-1-2">
                                    @foreach($posts as $k => $p)
                                        @if($k > 0 && $k < 5)
                                            @php
                                                $lang = $p->languages->first();
                                                $n = $lang->pivot->name;
                                                $d = $lang->pivot->description;
                                                $i = $p->image;
                                                $c = write_url($lang->pivot->canonical);
                                            @endphp
                                            <div class="mb20">
                                                <div class="handbook-item small">
                                                    <a href="{{ $c }}" class="image-wrap">
                                                        <img src="{{ $i }}" alt="{{ $n }}">
                                                        <div class="overlay"></div>
                                                        <div class="info-top">
                                                            <h3 class="title">{{ Str::limit($n, 60) }}</h3>
                                                            <div class="description">{{ Str::limit(strip_tags($d), 100) }}</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endforeach
@endif