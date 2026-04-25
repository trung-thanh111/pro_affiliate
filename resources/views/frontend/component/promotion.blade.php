@if(isset($promotionProducts) && count($promotionProducts))
    <section class="panel-promotion-modern mt50">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium">
                {{-- Sidebar --}}
                <div class="uk-width-large-1-4">
                    <div class="promotion-sidebar">
                        <div class="sidebar-head mb30">
                            <h2 class="heading-promotion">KHUYẾN MÃI</h2>
                            <div class="btn-all mt20">
                                <a href="javascript:void(0)" class="btn-orange-full">Xem tất cả</a>
                            </div>
                        </div>
                        <div class="sidebar-body">
                            @if(isset($categories) && !is_null($categories))
                                <ul class="category-list-sidebar uk-list">
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

                {{-- Products Grid --}}
                <div class="uk-width-large-3-4">
                    <div class="uk-grid uk-grid-small uk-grid-width-1-2 uk-grid-width-medium-1-2 uk-grid-width-large-1-4 product-grid-container" data-uk-grid-match="{target:'.modern-product-card'}">
                        @foreach($promotionProducts as $product)
                            <div class="mb20">
                                @include('frontend.component.product_card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @push('scripts')
        @vite(['resources/js/product_tab.js'])
    @endpush
@endif
