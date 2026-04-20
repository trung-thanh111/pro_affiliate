@if(isset($widgets['category']))
@php
    $categories = $widgets['category']->object;
    $columns = [];
    $catIndex = 0;
    $pattern = [1, 2, 2, 2, 1];
    $patternIndex = 0;

    while($catIndex < count($categories)) {
        $needed = $pattern[$patternIndex % count($pattern)];
        $columnItems = [];
        for($j = 0; $j < $needed && $catIndex < count($categories); $j++) {
            $columnItems[] = $categories[$catIndex++];
        }
        $columns[] = [
            'type' => $needed == 1 ? 'big' : 'small',
            'items' => $columnItems
        ];
        $patternIndex++;
    }
@endphp

<div class="panel-category-slider wow animate__animated animate__fadeIn">
    <div class="uk-container uk-container-center">
        <div class="panel-body">
            <div class="swiper-container swiper-category">
                <div class="swiper-wrapper">
                    @foreach($columns as $column)
                        <div class="swiper-slide column-{{ $column['type'] }}">
                            @foreach($column['items'] as $cat)
                                @php
                                    $name = $cat->languages->name;
                                    $canonical = write_url($cat->languages->canonical);
                                    $image = $cat->image;
                                    $count = $cat->products_count ?? 0;
                                @endphp
                                <a href="{{ $canonical }}" 
                                   class="category-card card-{{ $column['type'] }}"
                                   @if($column['type'] == 'small') 
                                       style="background-image: url('{{ $image }}');" 
                                   @endif
                                >
                                    <div class="info">
                                        <h3 class="title">{{ $name }}</h3>
                                        <span class="count">({{ $count }})</span>
                                    </div>
                                    @if($column['type'] == 'big')
                                        <div class="image">
                                            <img src="{{ $image }}" alt="{{ $name }}">
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <!-- Navigation -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </div>
</div>
@endif
