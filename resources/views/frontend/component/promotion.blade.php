@if(isset($promotionProducts) && count($promotionProducts))
    <section class="panel-promotion-modern mt50">
        <div class="uk-container uk-container-center">
            <div class="panel-head-modern">
                <div class="header-flex">
                    <h2 class="heading-modern">KHUYẾN MÃI</h2>
                    <div class="tab-scroller">
                        @if(isset($categories) && !is_null($categories))
                            <ul class="uk-tab-modern">
                                @foreach($categories->object as $key => $cat)
                                    @php
                                        if ($key > 8) break;
                                        $name = $cat->languages->name;
                                        $id = $cat->id;
                                    @endphp
                                    <li>
                                        <a href="javascript:void(0)" class="tab-title" data-id="{{ $id }}" title="{{ $name }}">
                                            {{ $name }}
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
                    @foreach($promotionProducts as $product)
                        <div class="mb20">
                            @include('frontend.component.product_card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @push('scripts')
        @vite(['resources/js/product_tab.js'])
    @endpush
@endif
