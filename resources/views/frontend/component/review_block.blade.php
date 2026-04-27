@if (isset($posts) && count($posts))
    <div class="row g-4 mb-5">
        @php
            $featured = $posts->first();
            $sideItems = $posts->slice(1, 3);

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
@endif
