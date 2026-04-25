@php
    $currentLanguage =
        $currentLanguage ?? (\App\Models\Language::where('canonical', app()->getLocale())->first()->id ?? 1);
@endphp

<div class="panel-category-slider wow animate__animated animate__fadeIn">
    <div class="container">
        <div class="panel-container">
            <div class="panel-head m-0">
                <h2 class="panel-title">
                    DANH MỤC
                </h2>
            </div>
            <div class="panel-body">
                <div class="swiper-container swiper-category">
                    <div class="swiper-wrapper">
                        @foreach ($categories->chunk(2) as $chunk)
                            <div class="swiper-slide">
                                @foreach ($chunk as $category)
                                    @php
                                        $name = $category->languages->first()->pivot->name ?? '';
                                        $canonical = $category->languages->first()->pivot->canonical ?? '#';
                                        $image = $category->image ?? 'https://placehold.co/80x80?text=No+Image';
                                    @endphp
                                    <a href="{{ route('router.index', ['canonical' => $canonical]) }}"
                                        class="category-item">
                                        <div class="category-image-wrapper">
                                            <img src="{{ $image }}" alt="{{ $name }}"
                                                class="category-image"
                                                onerror="this.src='https://placehold.co/80x80?text=Category'">
                                        </div>

                                        <div class="category-name">{{ $name }}</div>
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Navigation -->
                <div class="swiper-button-prev category-nav-prev"></div>
                <div class="swiper-button-next category-nav-next"></div>
            </div>

        </div>
    </div>
</div>
