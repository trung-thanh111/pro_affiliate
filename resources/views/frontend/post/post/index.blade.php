@extends('frontend.homepage.layout')
@section('content')

    <div id="art-detail" class="page-body bg-white pt-5 mt-3 pb-5">
        <div class="container">

            <!-- Hero Image Section (Master Header) -->
            <div class="post-hero-media mb-0 text-center position-relative">
                <div
                    class="image-container mx-auto rounded-4 shadow-sm overflow-hidden position-relative post-hero-container">
                    <img src="{{ $post->image }}" alt="{{ $post->languages->first()->pivot->name }}"
                        class="img-fluid w-100 h-100 object-fit-cover">

                    <!-- Overlay Header -->
                    <div
                        class="hero-overlay d-flex flex-column align-items-start justify-content-end p-4 p-md-5 pb-5 post-hero-overlay-offset">
                        <div class="post-breadcrumb-transparent mb-3">
                            <x-breadcrumb :breadcrumb="$breadcrumb" />
                        </div>
                        <h1 class="post-title-overlay display-5 fw-bold mb-3 text-white text-shadow text-start">
                            {{ $post->languages->first()->pivot->name }}
                        </h1>
                        <div
                            class="post-meta-overlay d-flex align-items-center justify-content-start gap-4 flex-wrap small text-white">
                            <span class="meta-item d-flex align-items-center gap-2">
                                <i class="bi bi-calendar3 text-primary"></i>
                                {{ convertDateTime($post->created_at, 'd/m/Y') }}
                            </span>
                            <span class="meta-item d-flex align-items-center gap-2">
                                <i class="bi bi-folder2-open text-primary"></i>
                                {{ $postCatalogue->languages->first()->pivot->name }}
                            </span>
                            <span class="meta-item d-flex align-items-center gap-2">
                                <i class="bi bi-eye text-primary"></i> {{ number_format($post->viewed) }} lượt xem
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area with Overlap -->
            <div class="row g-5 position-relative justify-content-center post-content-overlap-row">
                <div class="col-lg-12">
                    <div class="content-overlap-card bg-white p-4 p-md-5 rounded-4 border-0">

                        <!-- Comparison Table Section -->
                        @if (in_array($post->post_type, ['compare', 'review']) && $post->post_products->count() > 0)
                            <div class="comparison-outer-wrapper mb-5 pt-2">
                                <div class="comparison-header mb-4 text-center">
                                    <h2 class="fw-bold h3">Bảng so sánh thông số chi tiết</h2>
                                    <div class="heading-line mx-auto mb-2"></div>
                                    <p class="text-muted small">Giúp bạn đưa ra quyết định mua sắm chính xác nhất</p>
                                </div>
                                <div class="comparison-table-scroll-wrapper">
                                    <div class="table-responsive rounded-4 border shadow-sm">
                                        <table class="table table-bordered align-middle mb-0 bg-white">
                                            <thead class="bg-white sticky-top">
                                                <tr>
                                                    <th class="p-3 bg-light border-0 sticky-col">
                                                        <span class="text-uppercase small fw-bold text-muted">Tiêu chí so
                                                            sánh</span>
                                                    </th>
                                                    @foreach ($post->post_products as $pp)
                                                        <th
                                                            class="p-4 border-0 text-center product-header-cell @if ($pp->is_highlight) bg-warning bg-opacity-10 @endif">
                                                            @if ($pp->badge_text)
                                                                <div class="mb-3">
                                                                    <span
                                                                        class="badge bg-danger rounded-pill px-3 py-2 shadow-sm product-badge">
                                                                        {{ $pp->badge_text }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                            <div class="product-img mb-3 mx-auto product-img-comparison">
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
                                                            class="bg-light fw-bold py-3 px-4 text-uppercase small text-dark border-0">
                                                            <div class="section-title-sticky">
                                                                <i class="bi bi-layers-half me-2"></i>
                                                                {{ $section->title }}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @foreach ($section->rows as $row)
                                                        <tr class="criteria-row">
                                                            <td
                                                                class="fw-bold p-3 bg-light bg-opacity-25 border-start-0 sticky-col">
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
                            </div>
                        @endif

                        <div class="row g-5 mt-2">
                            <!-- Sidebar Column (LEFT - TOC) -->
                            <div class="col-lg-3">
                                <aside class="post-sidebar d-flex flex-column gap-4">
                                    <!-- Table of Contents -->
                                    <div id="toc-container" class="sidebar-card bg-light p-4 rounded-4 border-0 d-none">
                                        <h4 class="sidebar-title fw-bold mb-3 pb-2 border-bottom">
                                            Mục lục bài viết
                                        </h4>
                                        <nav id="toc" class="toc-nav"></nav>
                                    </div>

                                    <!-- Related Product Card -->
                                    @if ($post->product)
                                        <div class="sidebar-card bg-white p-4 rounded-4 shadow-sm border">
                                            <h5 class="fw-bold mb-3 border-bottom pb-2">
                                                Sản phẩm đánh giá
                                            </h5>
                                            <div class="related-product-item">
                                                <div class="product-thumb mb-3 rounded-3 overflow-hidden">
                                                    <a
                                                        href="{{ write_url($post->product->languages->first()->pivot->canonical) }}">
                                                        <img src="{{ $post->product->image }}"
                                                            alt="{{ $post->product->languages->first()->pivot->name }}"
                                                            class="w-100">
                                                    </a>
                                                </div>
                                                <h6 class="product-name fw-bold mb-2">
                                                    <a href="{{ write_url($post->product->languages->first()->pivot->canonical) }}"
                                                        class="text-decoration-none text-dark small">
                                                        {{ $post->product->languages->first()->pivot->name }}
                                                    </a>
                                                </h6>
                                                <div class="product-price text-danger fw-bold small">
                                                    {{ number_format($post->product->price) }}đ
                                                </div>
                                                <a href="{{ write_url($post->product->languages->first()->pivot->canonical) }}"
                                                    class="btn btn-primary w-100 mt-3 rounded-3 btn-sm">Xem chi tiết</a>
                                            </div>
                                        </div>
                                    @endif
                                </aside>
                            </div>

                            <!-- Main Column (MIDDLE) -->
                            <div class="col-lg-6 p-0">
                                <article class="post-main-content">
                                    @if (!empty($post->description))
                                        <div
                                            class="post-description lead fw-medium mb-5 text-secondary border-start border-4 border-primary ps-4">
                                            {!! $post->description !!}
                                        </div>
                                    @endif

                                    <div class="post-body-content mb-5 lh-lg text-dark">
                                        {!! $post->content !!}
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
                                                        <textarea class="form-control" placeholder="Nội dung bình luận" id="comment"></textarea>
                                                        <label for="comment">Nội dung bình luận</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-end">
                                                    <button type="submit"
                                                        class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm">Gửi
                                                        bình luận</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </article>
                            </div>

                            <!-- Sidebar Column (RIGHT) -->
                            <div class="col-lg-3">
                                <aside class="post-sidebar d-flex flex-column gap-4">
                                    <!-- Category Card (Tag Post) -->
                                    <div class="sidebar-card bg-light p-4 rounded-4 border-0">
                                        <h4 class="sidebar-title fw-bold mb-3 pb-2 border-bottom">
                                            Danh mục bài viết
                                        </h4>
                                        <ul class="sidebar-category-list list-unstyled m-0">
                                            @foreach ($post->post_catalogues as $cat)
                                                <li class="py-2 border-bottom last-child-border-0">
                                                    <a href="{{ write_url($cat->languages->first()->pivot->canonical) }}"
                                                        class="text-decoration-none text-dark d-flex justify-content-between align-items-center">
                                                        <span
                                                            class="fw-medium">{{ $cat->languages->first()->pivot->name }}</span>
                                                        <i class="bi bi-chevron-right x-small text-secondary"></i>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <!-- Social Share Card -->
                                    <div class="sidebar-card bg-light p-4 rounded-4 border-0">
                                        <h4 class="sidebar-title fw-bold mb-3 pb-2 border-bottom">
                                            Chia sẻ bài viết
                                        </h4>
                                        <div class="d-flex gap-3 justify-content-start mt-2">
                                            <a href="#" class="share-icon" title="Facebook"><i
                                                    class="bi bi-facebook"></i></a>
                                            <a href="#" class="share-icon" title="TikTok"><i
                                                    class="bi bi-tiktok"></i></a>
                                            <a href="#" class="share-icon" title="Twitter"><i
                                                    class="bi bi-twitter-x"></i></a>
                                        </div>
                                    </div>
                                </aside>
                            </div>
                        </div>
                    </div>
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
                <div class="related-posts-section mt-5 pt-5">
                    <h3 class="fw-bold mb-4 text-dark text-center">Bài viết liên quan</h3>
                    <div class="row g-4 justify-content-center">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.querySelector('.post-body-content');
            const toc = document.querySelector('#toc');
            const container = document.querySelector('#toc-container');

            if (content && toc) {
                const headings = content.querySelectorAll('h2, h3');
                if (headings.length > 0) {
                    container.classList.remove('d-none');
                    const ul = document.createElement('ul');
                    ul.className = 'list-unstyled m-0';

                    headings.forEach((heading, index) => {
                        const id = 'heading-' + index;
                        heading.id = id;

                        const li = document.createElement('li');
                        const isH2 = heading.tagName.toLowerCase() === 'h2';
                        li.className = isH2 ? 'toc-item toc-h2' : 'toc-item toc-h3 ps-4';

                        const a = document.createElement('a');
                        a.href = '#' + id;
                        a.className = 'text-decoration-none text-dark d-flex align-items-center py-2';

                        const span = document.createElement('span');
                        span.className = 'fw-medium';

                        let headingText = heading.textContent.trim();
                        headingText = headingText.replace(/^\d+[\.\)\s]+/, '');
                        span.textContent = headingText;

                        a.appendChild(span);
                        li.appendChild(a);
                        ul.appendChild(li);
                    });
                    toc.appendChild(ul);

                    // Smooth scroll
                    document.querySelectorAll('#toc a').forEach(anchor => {
                        anchor.addEventListener('click', function(e) {
                            e.preventDefault();
                            const targetId = this.getAttribute('href');
                            const targetElement = document.querySelector(targetId);
                            if (targetElement) {
                                const offset = 120; // Sticky header offset
                                const elementPosition = targetElement.getBoundingClientRect().top;
                                const offsetPosition = elementPosition + window.pageYOffset -
                                offset;

                                window.scrollTo({
                                    top: offsetPosition,
                                    behavior: "smooth"
                                });
                            }
                        });
                    });
                }
            }
        });
    </script>
@endsection
