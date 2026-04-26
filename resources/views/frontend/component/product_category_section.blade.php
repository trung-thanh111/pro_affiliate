@if(isset($categoryWithProducts) && count($categoryWithProducts))
    @foreach($categoryWithProducts as $categoryItem)
        @php
            $catName = $categoryItem->languages->first()->pivot->name ?? $categoryItem->name;
            $catCanonical = write_url($categoryItem->languages->first()->pivot->canonical ?? '');
        @endphp
        <section class="panel-category-products-modern mt50">
            <div class="uk-container uk-container-center">
                <div class="panel-head-modern">
                    <div class="header-flex">
                        <h2 class="heading-modern">{{ $catName }}</h2>
                        <div class="tab-scroller">
                            @php
                                $sidebarCats = (isset($categoryItem->children) && count($categoryItem->children)) 
                                    ? $categoryItem->children 
                                    : (isset($categories->object) ? $categories->object : []);
                            @endphp
                            @if(count($sidebarCats))
                                <ul class="uk-tab-modern">
                                    @foreach($sidebarCats as $key => $catItem)
                                        @php
                                            if ($key > 8) break;
                                            $catItemName = isset($catItem->languages->name) ? $catItem->languages->name : (isset($catItem->languages->first()->pivot->name) ? $catItem->languages->first()->pivot->name : '');
                                        @endphp
                                        <li>
                                            <a href="javascript:void(0)" class="tab-title @if($key == 0) active @endif" data-id="{{ $catItem->id }}" title="{{ $catItemName }}">
                                                {{ $catItemName }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="uk-grid uk-grid-small uk-grid-width-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-6 product-grid-container" data-uk-grid-match="{target:'.modern-product-card'}">
                        @foreach($categoryItem->products as $product)
                            <div class="mb20">
                                @include('frontend.component.product_card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endforeach
    @push('scripts')
        @vite(['resources/js/product_tab.js'])
    @endpush
@endif
