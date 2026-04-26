@if (isset($widgets['news']->object))
    @foreach ($widgets['news']->object as $key => $cat)
        @php
            $catName = $cat->languages->name;
            $catCanonical = write_url($cat->languages->canonical);
            $posts = $cat->posts ?? [];
        @endphp
        @if (count($posts))
            <section class="panel-review-handbook mt50 mb50">
                <div class="uk-container uk-container-center">
                    <div class="panel-head d-flex align-items-center justify-content-between mb30 flex-wrap gap-2">
                        <div class="head-left">
                            <h2 class="heading-standard">
                                <span>CẨM NANG REVIEW</span>
                            </h2>
                        </div>
                        <div class="head-right">
                            <a href="{{ $catCanonical }}" class="view-all-standard">
                                Xem tất cả <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="uk-grid uk-grid-medium">
                            {{-- Left Column: 5 small items --}}
                            <div class="uk-width-large-1-4">
                                <div class="handbook-list-left">
                                    @foreach ($posts->slice(1, 5) as $p)
                                        @php
                                            $lang = $p->languages->first();
                                            $n = $lang->pivot->name;
                                            $i = $p->image;
                                            $c = write_url($lang->pivot->canonical);
                                        @endphp
                                        <div class="handbook-small-item">
                                            <a href="{{ $c }}" class="thumb">
                                                <img src="{{ $i }}" alt="{{ $n }}">
                                            </a>
                                            <div class="info">
                                                <a href="{{ $c }}" class="title text-decoration-none">{{ $n }}</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Center Column: Featured --}}
                            <div class="uk-width-large-1-2">
                                @if (isset($posts[0]))
                                    @php
                                        $p1 = $posts[0];
                                        $lang1 = $p1->languages->first();
                                        $n1 = $lang1->pivot->name;
                                        $d1 = $lang1->pivot->description;
                                        $i1 = $p1->image;
                                        $c1 = write_url($lang1->pivot->canonical);
                                    @endphp
                                    <div class="handbook-featured">
                                        <a href="{{ $c1 }}" class="featured-img text-decoration-none">
                                            <img src="{{ $i1 }}" alt="{{ $n1 }}">
                                        </a>
                                        <a href="{{ $c1 }}"
                                            class="featured-title d-block text-decoration-none">{{ $n1 }}</a>
                                        <div class="featured-desc">{{ Str::limit(strip_tags($d1), 200) }}</div>
                                    </div>
                                @endif
                            </div>

                            {{-- Right Column: 2 card items --}}
                            <div class="uk-width-large-1-4">
                                <div class="handbook-cards-right">
                                    @foreach ($posts->slice(6, 2) as $p)
                                        @php
                                            $lang = $p->languages->first();
                                            $n = $lang->pivot->name;
                                            $i = $p->image;
                                            $c = write_url($lang->pivot->canonical);
                                        @endphp
                                        <div class="handbook-card-item">
                                            <a href="{{ $c }}" class="card-img text-decoration-none">
                                                <img src="{{ $i }}" alt="{{ $n }}">
                                            </a>
                                            <a href="{{ $c }}" class="card-title text-decoration-none">{{ $n }}</a>
                                        </div>
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
