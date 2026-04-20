@if(isset($bestSellers) && count($bestSellers))
    <div class="uk-container uk-container-center">
        <section class="panel-best-seller-modern">
            <div class="panel-head">
                <div class="uk-flex uk-flex-middle uk-flex-space-between header-flex">
                    <div class="head-left">
                        <h2 class="heading-best-seller">BÁN CHẠY</h2>
                        <div class="description">
                            Khám phá những sản phẩm dẫn đầu xu hướng và được hàng nghìn khách hàng tin dùng. Chất lượng vượt
                            trội, giá cả ưu đãi chỉ có tại hệ thống của chúng tôi.
                        </div>
                    </div>
                    <div class="head-right">
                        <a href="{{ route('product.index') }}" class="view-all-white">
                            Xem tất cả <i class="fa fa-angle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="uk-grid uk-grid-small uk-grid-width-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-5"
                    data-uk-grid-match="{target:'.modern-product-card'}">
                    @foreach($bestSellers as $product)
                        <div class="mb20">
                            @include('frontend.component.product_card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endif