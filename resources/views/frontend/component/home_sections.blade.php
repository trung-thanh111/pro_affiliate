@if (isset($homeSections) && count($homeSections))
    @foreach ($homeSections as $key => $section)
        @if (
            !$section->products ||
                (is_object($section->products) &&
                    $section->products->isEmpty() &&
                    (!isset($section->tabProductsMap) || empty($section->tabProductsMap))))
            @continue
        @endif

        @php
            $catUrl = write_url($section->category->canonical ?? '');
            $firstChildId = $section->children->first()?->id;
            $sectionId = 'home-section-' . str_replace('-', '', $key); // e.g. homesectionsection1
        @endphp

        <section class="panel-category-products-modern" id="{{ $sectionId }}">
            <div class="uk-container uk-container-center">
                <div class="panel-head-modern">
                    <div class="header-flex">
                        <h2 class="heading-modern">
                            <a href="{{ $catUrl }}" title="{{ $section->title }}">{{ $section->title }}</a>
                        </h2>
                        @if ($section->children->isNotEmpty())
                            <div class="tab-scroller">
                                <ul class="uk-tab-modern" id="{{ $sectionId }}-tabs">
                                    <li>
                                        <a href="javascript:void(0)" class="tab-title section-tab active"
                                            data-section="{{ $sectionId }}" data-tab-id="all" title="Tất cả">Tất
                                            cả</a>
                                    </li>
                                    @foreach ($section->children as $child)
                                        @php
                                            $childName = $child->name ?? '';
                                            $hasTabProducts =
                                                isset($section->tabProductsMap[$child->id]) &&
                                                $section->tabProductsMap[$child->id]->isNotEmpty();
                                        @endphp
                                        @if ($hasTabProducts)
                                            <li>
                                                <a href="javascript:void(0)" class="tab-title section-tab"
                                                    data-section="{{ $sectionId }}" data-tab-id="{{ $child->id }}"
                                                    title="{{ $childName }}">{{ $childName }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Panel "All" products --}}
                <div class="panel-body section-panel" id="{{ $sectionId }}-all" data-section="{{ $sectionId }}"
                    data-panel="all">
                    <div class="uk-grid uk-grid-small uk-grid-width-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-6 product-grid-container"
                        data-uk-grid-match="{target:'.modern-product-card'}">
                        @foreach ($section->products as $product)
                            <div class="mb20">
                                @include('frontend.component.product_card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach ($section->children as $child)
                    @if (isset($section->tabProductsMap[$child->id]) && $section->tabProductsMap[$child->id]->isNotEmpty())
                        <div class="panel-body section-panel d-none" id="{{ $sectionId }}-{{ $child->id }}"
                            data-section="{{ $sectionId }}" data-panel="{{ $child->id }}">
                            <div class="uk-grid uk-grid-small uk-grid-width-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-6 product-grid-container"
                                data-uk-grid-match="{target:'.modern-product-card'}">
                                @foreach ($section->tabProductsMap[$child->id] as $product)
                                    <div class="mb20">
                                        @include('frontend.component.product_card', [
                                            'product' => $product,
                                        ])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endforeach

    <script>
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.section-tab').forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        var sectionId = this.dataset.section;
                        var tabId = this.dataset.tabId;

                        // Update active tab
                        document.querySelectorAll('[data-section="' + sectionId +
                            '"].section-tab').forEach(function(t) {
                            t.classList.remove('active');
                        });
                        this.classList.add('active');

                        // Show/hide panels
                        document.querySelectorAll('[data-section="' + sectionId +
                            '"].section-panel').forEach(function(panel) {
                            if (panel.dataset.panel === tabId) {
                                panel.classList.remove('d-none');
                            } else {
                                panel.classList.add('d-none');
                            }
                        });
                    });
                });
            });
        })();
    </script>
@endif
