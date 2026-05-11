@php
    $keyword = App\Enums\SlideEnum::SECONDHOME;
@endphp

@if(!empty($slides[$keyword]['item']))
<section class="second-home-slider-section mb40">
    <div class="uk-container uk-container-center">
        <div class="swiper-container second-home-slider rounded-slider">
            <div class="swiper-wrapper">
                @foreach($slides[$keyword]['item'] as $key => $val)
                    <div class="swiper-slide">
                        <a href="{{ $val['canonical'] ?? 'javascript:void(0)' }}" class="slide-link">
                            <div class="slide-image">
                                <img src="{{ $val['image'] }}" alt="{{ $val['name'] ?? 'Banner' }}" />
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
@endif
