@php
    $slideKeyword = App\Enums\SlideEnum::MAIN;
@endphp

@if(!empty($slides[$slideKeyword]['item']))
    <div class="panel-slide page-setup" data-setting="">
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($slides[$slideKeyword]['item'] as $key => $val)
                    <div class="swiper-slide">
                        <div class="slide-image">
                            <img src="{{ $val['image'] }}" alt="{{ $val['name'] }}" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif