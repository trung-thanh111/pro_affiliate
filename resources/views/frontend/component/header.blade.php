@php
    $wishlistCount = $wishlistCount ?? \Gloudemans\Shoppingcart\Facades\Cart::instance('wishlist')->count();
    $compareCount = $compareCount ?? \Gloudemans\Shoppingcart\Facades\Cart::instance('compare')->count();
    $cartCount = $cartCount ?? \Gloudemans\Shoppingcart\Facades\Cart::instance('shopping')->count();
@endphp
<header class="pc-header d-none d-lg-block">
    {{-- Top Bar --}}
    <div class="top-bar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="text-decoration-none">Kênh Người Bán</a>
                    <div class="divider"></div>
                    <a href="#" class="text-decoration-none">Trở thành Người bán Shopee</a>
                    <div class="divider"></div>
                    <a href="#" class="text-decoration-none">Tải ứng dụng</a>
                    <div class="divider"></div>
                    <span class="d-flex align-items-center gap-2">Kết nối <i class="bi bi-facebook"></i> <i class="bi bi-instagram"></i></span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="text-decoration-none"><i class="bi bi-bell"></i> Thông Báo</a>
                    <a href="#" class="text-decoration-none"><i class="bi bi-question-circle"></i> Hỗ Trợ</a>
                    <a href="#" class="text-decoration-none"><i class="bi bi-globe"></i> Tiếng Việt <i class="bi bi-chevron-down"></i></a>
                    <div class="divider"></div>
                    @guest('customer')
                        <a href="{{ route('customer.login') }}" class="text-decoration-none">Đăng Ký</a>
                        <div class="divider"></div>
                        <a href="{{ route('customer.login') }}" class="text-decoration-none">Đăng Nhập</a>
                    @endguest
                    @auth('customer')
                        <a href="{{ route('customer.account') }}" class="user-info text-decoration-none d-flex align-items-center gap-1">
                            <i class="bi bi-person-circle"></i>
                            {{ auth('customer')->user()->name }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- Main Header --}}
    <div class="main-header">
        <div class="container">
            <div class="d-flex align-items-start gap-4">
                {{-- Logo --}}
                <div class="logo flex-shrink-0 mt-1">
                    <a href="/"><img src="{{ $system['homepage_logo'] }}" alt="Logo"></a>
                </div>

                {{-- Search Area --}}
                <div class="header-center">
                    <div class="header-search">
                        <form action="{{ route('product.catalogue.search') }}" method="GET" class="search-form">
                            <input type="text" name="keyword" class="input-search" placeholder="Shopee bao lễ - Gì cũng rẻ" autocomplete="off">
                            <button type="submit" class="btn-search">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                        <div class="search-suggestions">
                            {{-- Results via AJAX --}}
                        </div>
                    </div>
                    
                    {{-- Category/Tags below search --}}
                    @if(isset($headerTags) && count($headerTags))
                        <div class="header-tags">
                            @foreach($headerTags as $tag)
                                @php
                                    $tagName = $tag->languages->first()->pivot->name ?? '';
                                    $tagUrl = $tag->languages->first()->pivot->canonical ?? '#';
                                @endphp
                                @if($tagName)
                                    <a href="{{ route('product.catalogue.index', ['canonical' => $tagUrl]) }}">{{ $tagName }}</a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Cart --}}
                <div class="cart-wrapper">
                    <a href="{{ route('cart.checkout') }}" class="btn-cart">
                        <i class="bi bi-cart3"></i>
                        <span class="cart-count">{{ $cartCount }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header><!-- .header -->
<header class="mobile-header d-lg-none">
    <div class="container-fluid">
        <div class="header-top-row d-flex align-items-center justify-content-between py-2">
            {{-- Left: Logo --}}
            <div class="mobile-logo">
                <a href="/"><img src="{{ $system['homepage_logo'] }}" alt="Logo" class="img-fluid"
                        style="max-height: 45px;"></a>
            </div>

            {{-- Center: Search (Visible only on Tablet) --}}
            <div class="header-search-tablet d-none d-md-block flex-grow-1 mx-4">
                <div class="mobile-search-bar">
                    <form action="{{ route('product.catalogue.search') }}" method="GET" class="search-form-mobile">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control"
                                placeholder="Bạn muốn tìm gì hôm nay?" autocomplete="off">
                            <button class="btn btn-search-mobile" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right: Icons (Menu + Cart) --}}
            <div class="mobile-actions d-flex align-items-center gap-2">
                <a href="{{ route('cart.checkout') }}" class="mobile-icon-link position-relative">
                    <i class="bi bi-cart3 fs-2"></i>
                    <span class="cart-badge">{{ $cartCount }}</span>
                </a>
                <div class="mobile-nav-toggle">
                    <a class="mobile-menu-trigger" href="#offcanvas" data-uk-offcanvas="{target:'#offcanvas'}">
                        <i class="bi bi-list fs-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Bottom Row: Search (Visible only on Mobile) --}}
        <div class="header-search-row pb-3 d-md-none">
            <div class="mobile-search-bar">
                <form action="{{ route('product.catalogue.search') }}" method="GET" class="search-form-mobile">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Bạn muốn tìm gì hôm nay?"
                            autocomplete="off">
                        <button class="btn btn-search-mobile" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header><!-- .mobile-header -->

<!-- Mobile Menu Offcanvas -->
<div id="offcanvas" class="uk-offcanvas">
    <div class="uk-offcanvas-bar uk-offcanvas-bar-flip mobile-menu-offcanvas">
        <button class="uk-offcanvas-close mobile-menu-close" type="button">
            <i class="fa fa-times"></i>
        </button>

        <div class="mobile-menu-header">
            <div class="mobile-menu-logo">
                <a href="/" title="Logo">
                    <img src="{{ $system['homepage_logo'] }}" alt="Logo" />
                </a>
            </div>
        </div>

        <nav class="mobile-menu-nav">
            <ul class="uk-nav uk-nav-offcanvas mobile-menu-list">
                {!! $menu['main-menu'] ?? '' !!}
            </ul>
        </nav>

        <div class="mobile-menu-footer">
            <div class="mobile-menu-actions">
                @guest('customer')
                    <a href="{{ route('customer.login') }}" class="mobile-menu-btn mobile-btn-login">
                        <i class="fa fa-user"></i> Đăng nhập
                    </a>
                @endguest

                @auth('customer')
                    <a href="{{ route('customer.account') }}" class="mobile-menu-btn mobile-btn-account">
                        <i class="fa fa-user-circle"></i> Tài khoản
                    </a>
                    <form action="{{ route('customer.logout') }}" method="POST" class="mobile-logout-form">
                        @csrf
                        <button type="submit" class="mobile-menu-btn mobile-btn-logout">
                            <i class="fa fa-sign-out"></i> Đăng xuất
                        </button>
                    </form>
                @endauth

                <a href="{{ route('product.wishlist.index') }}" class="mobile-menu-btn mobile-btn-wishlist">
                    <i class="fa fa-heart"></i> Yêu thích
                    @if($wishlistCount > 0)
                        <span class="mobile-badge">{{ $wishlistCount }}</span>
                    @endif
                </a>

                <a href="{{ route('cart.checkout') }}" class="mobile-menu-btn mobile-btn-cart">
                    <i class="fa fa-shopping-cart"></i> Giỏ hàng
                    @if($cartCount > 0)
                        <span class="mobile-badge">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>

            <div class="mobile-menu-contact">
                <div class="mobile-contact-item">
                    <i class="fa fa-phone"></i>
                    <a href="tel:{{ $system['contact_hotline'] }}">{{ $system['contact_hotline'] }}</a>
                </div>
                <div class="mobile-contact-item">
                    <i class="fa fa-envelope"></i>
                    <a href="mailto:{{ $system['contact_email'] }}">{{ $system['contact_email'] }}</a>
                </div>
            </div>
        </div>
    </div>
</div>