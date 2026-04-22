@extends('frontend.homepage.layout')
@section('content')

    <div id="art-detail" class="page-body bg-light pb-5">

        <div class="container container-center">
            <div class="breadcrumb-wrapper">
                <x-breadcrumb :breadcrumb="$breadcrumb" />
            </div>
        </div>

        <!-- Hero Header Section -->
        <div class="post-hero-section position-relative overflow-hidden mb-5">
            <div class="post-hero-image" style="background-image: url('{{ $post->image }}');"></div>
            <div class="post-hero-overlay d-flex align-items-end">
                <div class="uk-container uk-container-center w-100 py-5">
                    <div class="post-hero-content text-start text-white">
                        <h1 class="post-title display-4 fw-bold mb-4 text-white">
                            {{ $post->languages->first()->pivot->name }}
                        </h1>
                        <div class="post-meta d-flex align-items-center justify-content-start gap-4 flex-wrap">
                            <span class="meta-item d-flex align-items-center gap-2 text-white">
                                <i class="bi bi-calendar3"></i> {{ convertDateTime($post->created_at, 'd/m/Y') }}
                            </span>
                            <span class="meta-item d-flex align-items-center gap-2 text-white">
                                <i class="bi bi-folder2-open"></i> {{ $postCatalogue->languages->first()->pivot->name }}
                            </span>
                            <span class="meta-item d-flex align-items-center gap-2 text-white">
                                <i class="bi bi-eye"></i> {{ number_format($post->viewed) }} lượt xem
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="uk-container uk-container-center">
            <div class="row g-5">
                <!-- Main Column (9) -->
                <div class="col-lg-9">
                    <article class="post-main-content bg-white p-4 p-md-5 rounded-4 shadow-sm">
                        @if (!empty($post->description))
                            <div
                                class="post-description lead fw-medium mb-4 text-secondary border-start border-4 border-primary ps-4">
                                {!! $post->description !!}
                            </div>
                            @if (in_array($post->post_type, ['compare', 'review']) && $post->post_products->count() > 0)
                                <div class="comparison-table-wrapper mb-5 overflow-hidden">
                                    <div class="table-responsive rounded-4 border shadow-sm">
                                        <table class="table table-bordered align-middle mb-0 bg-white">
                                            <thead class="sticky-top bg-white z-2 shadow-sm">
                                                <tr>
                                                    <th class="p-3 bg-light border-0"
                                                        style="min-width: 200px; width: 200px; position: sticky; left: 0; z-index: 3;">
                                                        <span class="text-uppercase small fw-bold text-muted">Tiêu chí so
                                                            sánh</span>
                                                    </th>
                                                    @foreach ($post->post_products as $pp)
                                                        <th class="p-4 border-0 text-center @if ($pp->is_highlight) bg-warning bg-opacity-10 @endif"
                                                            style="min-width: 280px;">
                                                            @if ($pp->badge_text)
                                                                <div class="mb-3">
                                                                    <span
                                                                        class="badge bg-danger rounded-pill px-3 py-2 shadow-sm"
                                                                        style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                                                                        {{ $pp->badge_text }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                            <div class="product-img mb-3 mx-auto"
                                                                style="width: 120px; height: 120px;">
                                                                <img src="{{ $pp->product->image }}"
                                                                    alt="{{ $pp->column_title ?: $pp->product->languages->first()->pivot->name }}"
                                                                    class="img-fluid rounded-3 h-100 w-100 object-fit-contain">
                                                            </div>
                                                            <h4 class="h6 fw-bold mb-3 product-name-fe">
                                                                {{ $pp->column_title ?: $pp->product->languages->first()->pivot->name ?? '' }}
                                                            </h4>
                                                            <a href="{{ $pp->product->link ?? '#' }}"
                                                                class="btn btn-primary btn-sm w-100 fw-bold rounded-3 py-2"
                                                                target="_blank" rel="nofollow">
                                                                Kiểm tra giá rẻ nhất
                                                            </a>
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($post->comparison_sections as $section)
                                                    <tr class="section-divider">
                                                        <td colspan="{{ $post->post_products->count() + 1 }}"
                                                            class="bg-light fw-bold py-3 px-4 text-uppercase small text-dark border-0"
                                                            style="letter-spacing: 1px; color: #1ab394 !important;">
                                                            <i class="bi bi-layers-half me-2"></i> {{ $section->title }}
                                                        </td>
                                                    </tr>
                                                    @foreach ($section->rows as $row)
                                                        <tr class="criteria-row">
                                                            <td class="fw-bold p-3 bg-light bg-opacity-25 border-start-0"
                                                                style="position: sticky; left: 0; background: #fdfdfd; z-index: 1;">
                                                                {{ $row->label }}
                                                            </td>
                                                            @foreach ($post->post_products as $pp)
                                                                @php
                                                                    $cell = $row->cells
                                                                        ->where('post_product_id', $pp->id)
                                                                        ->first();
                                                                @endphp
                                                                <td class="text-center p-3 border-end-0">
                                                                    @if ($cell)
                                                                        <div
                                                                            class="cell-value @if ($cell->is_highlight) fw-bold text-primary @endif">
                                                                            {!! nl2br(e($cell->value_text)) !!}
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted opacity-50">-</span>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <style>
                                    .comparison-table-wrapper .sticky-top {
                                        top: -1px;
                                    }

                                    .product-name-fe {
                                        display: -webkit-box;
                                        -webkit-line-clamp: 2;
                                        -webkit-box-orient: vertical;
                                        overflow: hidden;
                                        min-height: 2.8em;
                                    }

                                    .criteria-row:hover td {
                                        background-color: #f8f9fa;
                                    }

                                    .table-responsive::-webkit-scrollbar {
                                        height: 6px;
                                    }

                                    .table-responsive::-webkit-scrollbar-thumb {
                                        background: #cbd5e0;
                                        border-radius: 10px;
                                    }
                                </style>
                            @endif
                        @endif

                        <div class="post-body-content mb-5">
                            {!! $post->content !!}
                        </div>

                        <!-- Social Share Section -->
                        <div class="social-share-section border-top pt-4 mb-5">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <span class="fw-bold text-dark">Chia sẻ bài viết:</span>
                                <div class="d-flex gap-2">
                                    <a href="#" class="share-btn share-fb" title="Facebook"><i
                                            class="bi bi-facebook"></i></a>
                                    <a href="#" class="share-btn share-tt" title="TikTok"><i
                                            class="bi bi-tiktok"></i></a>
                                    <a href="#" class="share-btn share-tw" title="Twitter"><i
                                            class="bi bi-twitter-x"></i></a>
                                    <a href="#" class="share-btn share-li" title="LinkedIn"><i
                                            class="bi bi-linkedin"></i></a>
                                    <a href="#" class="share-btn share-pt" title="Pinterest"><i
                                            class="bi bi-pinterest"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Comment Section -->
                        <div class="comment-section border-top pt-5">
                            <h3 class="fw-bold mb-4">Để lại bình luận</h3>
                            <form action="#" method="POST" class="comment-form">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="name"
                                                placeholder="Họ tên của bạn">
                                            <label for="name">Họ tên của bạn</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="email"
                                                placeholder="Email (không bắt buộc)">
                                            <label for="email">Email (không bắt buộc)</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" placeholder="Nội dung bình luận" id="comment" style="height: 120px"></textarea>
                                            <label for="comment">Nội dung bình luận</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">Gửi
                                            bình
                                            luận</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </article>
                </div>

                <!-- Sidebar Column (3) -->
                <div class="col-lg-3">
                    <aside class="post-sidebar d-flex flex-column gap-4">
                        <!-- Category Card -->
                        <div class="sidebar-card bg-white p-4 rounded-4 shadow-sm">
                            <h4 class="sidebar-title fw-bold mb-3 pb-2 border-bottom">Danh mục</h4>
                            <ul class="sidebar-category-list list-unstyled m-0">
                                @if (isset($widgets['product-category']->object))
                                    @foreach ($widgets['product-category']->object as $cat)
                                        <li class="py-2 border-bottom last-child-border-0">
                                            <a href="{{ write_url($cat->languages->first()->pivot->canonical) }}"
                                                class="text-decoration-none text-dark d-flex justify-content-between align-items-center">
                                                <span>{{ $cat->languages->first()->pivot->name }}</span>
                                                <i class="bi bi-chevron-right small text-secondary"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        <!-- Banner Ad -->
                        <div class="sidebar-banner rounded-3 overflow-hidden shadow-sm">
                            <a href="#">
                                <img src="{{ $post->banner ?? '/templates/frontend/resources/img/banner-sidebar.jpg' }}"
                                    alt="Banner Ad" class="w-100 rounded-2 shadow-sm">
                            </a>
                        </div>

                        <!-- Related Product Card -->
                        @if ($post->product)
                            <div class="sidebar-card bg-white p-4 rounded-4 shadow-sm">
                                <h4 class="sidebar-title fw-bold mb-3 pb-2 border-bottom">Sản phẩm liên quan</h4>
                                <div class="related-product-item">
                                    <div class="product-thumb mb-3 rounded-3 overflow-hidden">
                                        <a href="{{ write_url($post->product->languages->first()->pivot->canonical) }}">
                                            <img src="{{ $post->product->image }}"
                                                alt="{{ $post->product->languages->first()->pivot->name }}"
                                                class="w-100">
                                        </a>
                                    </div>
                                    <h5 class="product-name fw-bold mb-2">
                                        <a href="{{ write_url($post->product->languages->first()->pivot->canonical) }}"
                                            class="text-decoration-none text-dark">
                                            {{ $post->product->languages->first()->pivot->name }}
                                        </a>
                                    </h5>
                                    <div class="product-price text-primary fw-bold">
                                        {{ number_format($post->product->price) }}đ
                                    </div>
                                    <a href="{{ write_url($post->product->languages->first()->pivot->canonical) }}"
                                        class="btn btn-outline-primary w-100 mt-3 rounded-3 btn-sm">Xem chi tiết</a>
                                </div>
                            </div>
                        @endif
                    </aside>
                </div>
            </div>

            <!-- Related Posts Section -->
            @php
                $relatedPosts = $post->related_posts;
                if ($relatedPosts->isEmpty()) {
                    $relatedPosts = $postCatalogue->posts->where('id', '!=', $post->id)->take(4);
                }
            @endphp
            @if ($relatedPosts->count() > 0)
                <div class="related-posts-section mt-5 pt-5 border-top">
                    <h3 class="fw-bold mb-4 text-dark">Bài viết liên quan</h3>
                    <div class="row g-4">
                        @foreach ($relatedPosts as $val)
                            <div class="col-lg-3 col-md-6">
                                @include('frontend.component.post_card', ['post' => $val])
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
