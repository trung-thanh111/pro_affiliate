@php
    $slideKeyword = App\Enums\SlideEnum::MAIN;
    $sideKeyword = App\Enums\SlideEnum::SIDE_BANNER;
@endphp

<div class="panel-slide page-setup">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-main">
                @if(!empty($slides[$slideKeyword]['item']))
                    <div class="swiper-container main-slider">
                        <div class="swiper-wrapper">
                            @foreach($slides[$slideKeyword]['item'] as $key => $val)
                                <div class="swiper-slide">
                                    <div class="slide-image">
                                        <img src="{{ $val['image'] }}" alt="{{ $val['name'] ?? 'Banner' }}" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                @endif
            </div>
            <div class="hero-side">
                @if(!empty($slides[$sideKeyword]['item']))
                    @foreach($slides[$sideKeyword]['item'] as $key => $val)
                        @if($loop->index < 2)
                            <div class="side-banner-item">
                                <div class="side-image">
                                    <img src="{{ $val['image'] }}" alt="{{ $val['name'] ?? 'Banner' }}" />
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    {{-- Mock Fallback for UI demonstration --}}
                    <div class="side-banner-item">
                        <div class="side-image">
                            <img src="userfiles/image/banner/promo-banner.png" alt="Promo 1" />
                        </div>
                    </div>
                    <div class="side-banner-item">
                        <div class="side-image">
                            <img src="userfiles/image/banner/tech-banner.png" alt="Promo 2" />
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>