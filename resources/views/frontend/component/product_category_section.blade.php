@if(isset($categoryWithProducts) && count($categoryWithProducts))
    @foreach($categoryWithProducts as $categoryItem)
        @php
            $catName = $categoryItem->languages->first()->pivot->name ?? $categoryItem->name;
            $catCanonical = write_url($categoryItem->languages->first()->pivot->canonical ?? '');
        @endphp
        <section class="panel-category-products-modern mt50">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium">
                    {{-- Sidebar --}}
                    <div class="uk-width-large-1-4">
                        <div class="promotion-sidebar">
                            <div class="sidebar-head mb30">
                                <h2 class="heading-promotion">{{ $catName }}</h2>
                                <div class="btn-all mt20">
                                    <a href="{{ $catCanonical }}" class="btn-orange-full">Xem tất cả</a>
                                </div>
                            </div>
                            <div class="sidebar-body">
                                @php
                                    $sidebarCats = (isset($categoryItem->children) && count($categoryItem->children)) 
                                        ? $categoryItem->children 
                                        : (isset($categories->object) ? $categories->object : []);
                                @endphp
                                @if(count($sidebarCats))
                                    <ul class="category-list-sidebar uk-list">
                                        @foreach($sidebarCats as $key => $catItem)
                                            @php
                                                if ($key > 8) break;
                                                $catItemName = isset($catItem->languages->name) ? $catItem->languages->name : (isset($catItem->languages->first()->pivot->name) ? $catItem->languages->first()->pivot->name : '');
                                                $catItemCanonical = write_url(isset($catItem->languages->canonical) ? $catItem->languages->canonical : (isset($catItem->languages->first()->pivot->canonical) ? $catItem->languages->first()->pivot->canonical : ''));
                                            @endphp
                                            <li>
                                                <a href="{{ $catItemCanonical }}" title="{{ $catItemName }}">
                                                    {{ $catItemName }}
                                                    <i class="fa fa-angle-right"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Products Grid --}}
                    <div class="uk-width-large-3-4">
                        <div class="uk-grid uk-grid-small uk-grid-width-1-2 uk-grid-width-medium-1-2 uk-grid-width-large-1-4" data-uk-grid-match="{target:'.modern-product-card'}">
                            @foreach($categoryItem->products as $product)
                                <div class="mb20">
                                    @include('frontend.component.product_card', ['product' => $product])
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endforeach
@endif
