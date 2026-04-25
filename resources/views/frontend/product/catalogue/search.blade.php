@extends('frontend.homepage.layout')

@section('content')
    <div id="prd-search" class="page-body py-5">
        <div class="container">
            <div class="prd-catalogue-header mb-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item"><a href="{{ write_url('/') }}" class="text-decoration-none">Trang chủ</a></li>
                                <li class="breadcrumb-item active">Tìm kiếm</li>
                            </ol>
                        </nav>
                        <h1 class="display-6 fw-bold text-dark mb-0">{{ $seo['meta_title'] }}</h1>
                    </div>
                </div>
            </div>

            <div class="product-grid">
                @if(!is_null($products) && count($products))
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
                        @foreach ($products as $product)
                            <div class="col">
                                @include('frontend.component.product_card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 d-flex justify-content-center search-paginate">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                        <img src="/backend/img/no-product.png" alt="No product" class="img-fluid mb-3" style="max-width: 150px; opacity: 0.5;">
                        <h4 class="text-dark fw-bold">Không tìm thấy kết quả</h4>
                        <p class="text-muted">Chúng tôi không tìm thấy sản phẩm nào phù hợp với từ khóa của bạn.</p>
                        <a href="{{ write_url('/') }}" class="btn btn-primary rounded-pill px-4">Quay lại trang chủ</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

