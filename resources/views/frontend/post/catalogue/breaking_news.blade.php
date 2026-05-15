@extends('frontend.homepage.layout')

@section('content')
    <div id="breaking-news-page" class="page-body py-5" style="background-color: #f8f9fa;">
        {{-- Breadcrumb --}}
        <div class="uk-container uk-container-center mb-4">
            <x-breadcrumb :breadcrumb="$breadcrumb" />
        </div>

        <div class="uk-container uk-container-center">
            {{-- Header Section --}}
            <div class="art-catalogue-wrapper mb-5">
                <h2 class="heading-2"><span>Tin Nhanh 24h</span></h2>
                <div class="d-flex align-items-center justify-content-between mt-3 px-3">
                    <p class="text-muted mb-0">Cập nhật những tin tức nóng hổi và mới nhất vừa diễn ra.</p>
                    <div class="d-none d-md-block text-end">
                        <div class="fw-bold fs-5 text-primary">{{ now()->format('H:i') }} <span
                                class="text-muted fw-normal ms-2">{{ now()->format('d/m/Y') }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="uk-grid uk-grid-medium" id="breaking-news-container">
                @if (count($posts))
                    {{-- Featured Big Card (Only on Page 1) --}}
                    @if ($posts->currentPage() == 1)
                        @php
                            $allPosts = $posts->getCollection();
                            $firstPost = $allPosts->shift();
                        @endphp
                        @if ($firstPost)
                            @php
                                $firstTitle = $firstPost->languages->first()->pivot->name ?? $firstPost->name;
                                $firstImage = $firstPost->image;
                                $firstHref = write_url($firstPost->languages->first()->pivot->canonical ?? '');
                                $firstDesc = cutnchar(
                                    strip_tags($firstPost->languages->first()->pivot->description ?? ''),
                                    450,
                                );

                                $product = $firstPost->product ?? null;
                                $fAffiliateUrl = !empty($product->link) ? $product->link : $fallbackAffiliateUrl ?? '';
                            @endphp

                            <div class="uk-width-1-1 mb-5">
                                <div class="featured-breaking-card position-relative overflow-hidden rounded-4 shadow-lg js-news-item"
                                    data-affiliate="{{ $fAffiliateUrl }}" data-href="{{ $firstHref }}">
                                    <a href="{{ $firstHref }}" class="d-block text-decoration-none js-news-link">
                                        <div class="ratio ratio-21x9 overflow-hidden bg-dark">
                                            <img src="{{ $firstImage }}" alt="{{ $firstTitle }}"
                                                class="img-fluid object-fit-cover w-100 h-100 transition-transform duration-700">
                                        </div>
                                        <div class="card-overlay position-absolute bottom-0 start-0 w-100 p-4 p-lg-5"
                                            style="background: linear-gradient(0deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0) 100%);">
                                            <span class="badge bg-danger mb-3 animate-flash">BREAKING NEWS</span>
                                            <h2 class="text-white display-5 fw-bold mb-3 line-clamp-2">{{ $firstTitle }}
                                            </h2>
                                            <div class="text-white-50 line-clamp-3 mb-0 d-none d-md-block fs-5">
                                                {!! $firstDesc !!}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        @php $allPosts = $posts; @endphp
                    @endif

                    {{-- Grid of news --}}
                    <div class="uk-width-1-1">
                        <div class="uk-grid uk-grid-medium" id="load-more-target">
                            @include('frontend.post.catalogue.component.breaking_news_item', [
                                'posts' => $allPosts,
                            ])
                        </div>
                    </div>

                    {{-- Load More Button --}}
                    @if ($posts->hasMorePages())
                        <div class="uk-width-1-1 text-center mt-5">
                            <button class="btn btn-load-more-breaking rounded-pill px-5 py-2 fw-bold border-2"
                                data-page="2"
                                style="border: 2px solid #FF7A45; color: #FF7A45; background: transparent; transition: all 0.3s;">
                                XEM THÊM <i class="bi bi-chevron-down ms-2"></i>
                            </button>
                        </div>
                    @endif
                @else
                    <div class="uk-width-1-1">
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                            <i class="bi bi-newspaper display-1 text-muted mb-3 d-block"></i>
                            <h3 class="text-muted">Hiện chưa có tin nhanh nào được cập nhật.</h3>
                            <p class="text-secondary">Vui lòng quay lại sau ít phút.</p>
                            <a href="{{ route('home.index') }}"
                                class="btn btn-primary-gradient px-4 py-2 mt-3 text-white text-decoration-none rounded-pill">Về
                                trang chủ</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Handle Load More
                $('.btn-load-more-breaking').on('click', function() {
                    let btn = $(this);
                    let page = btn.data('page');

                    btn.html('<span class="spinner-border spinner-border-sm me-2"></span> ĐANG TẢI...');
                    btn.prop('disabled', true);

                    $.ajax({
                        url: '{{ route('ajax.post.loadBreakingNews') }}',
                        type: 'GET',
                        data: {
                            page: page
                        },
                        success: function(res) {
                            if (res.html != '') {
                                $('#load-more-target').append(res.html);
                                btn.data('page', page + 1);
                                btn.html('XEM THÊM <i class="bi bi-chevron-down ms-2"></i>');
                                btn.prop('disabled', false);

                                if (!res.hasMore) {
                                    btn.parent().fadeOut();
                                }
                            } else {
                                btn.parent().fadeOut();
                            }
                        },
                        error: function() {
                            btn.html('XEM THÊM <i class="bi bi-chevron-down ms-2"></i>');
                            btn.prop('disabled', false);
                        }
                    });
                });
            });
        </script>
    @endpush

    <style>
        #breaking-news-page {
            --primary-gradient: linear-gradient(135deg, #FF7A45 0%, #FF9F43 100%);
            --danger-soft: rgba(220, 53, 69, 0.1);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .x-small {
            font-size: 0.75rem;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .transition-all {
            transition: all 0.3s ease-in-out;
        }

        .duration-500 {
            transition-duration: 500ms;
        }

        .duration-700 {
            transition-duration: 700ms;
        }

        .animate-flash {
            animation: flash-animation 2s infinite;
        }

        @keyframes flash-animation {

            0%,
            50%,
            100% {
                opacity: 1;
            }

            25%,
            75% {
                opacity: 0.6;
            }
        }

        .featured-breaking-card .card-overlay {
            z-index: 2;
        }

        .featured-breaking-card:hover img {
            transform: scale(1.05);
        }

        .news-item-premium:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }

        .news-item-premium:hover img {
            transform: scale(1.05);
        }

        .btn-readmore {
            position: relative;
            padding-right: 5px;
            transition: all 0.2s;
        }

        .btn-readmore:hover {
            padding-right: 10px;
            color: #FF7A45 !important;
        }

        .hover-text-primary:hover {
            color: #FF7A45 !important;
        }

        .btn-primary-gradient {
            background: var(--primary-gradient);
            box-shadow: 0 4px 15px rgba(255, 122, 69, 0.3);
            transition: all 0.3s;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 122, 69, 0.4);
            opacity: 0.95;
        }

        .btn-load-more-breaking:hover {
            background-color: #FF7A45 !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(255, 122, 69, 0.3);
        }
    </style>
@endsection
