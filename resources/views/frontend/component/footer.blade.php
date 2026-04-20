<footer class="footer py-5 text-white">
    <div class="uk-container uk-container-center">
        <div class="footer-upper">
            <div class="row g-4 align-items-start">
                {{-- Column 1: Logo --}}
                <div class="col-lg-3 col-md-6">
                    <div class="footer-col">
                        <div class="footer-logo mb-4">
                            <a href="/"><img src="{{ $system['homepage_logo'] }}" alt="Logo" style="max-height: 70px; width: auto;"></a>
                        </div>
                        <div class="footer-slogan opacity-50 small mt-3">
                            {{ $system['homepage_company'] ?? 'T-shop - Crafted with passion for fine wine & spirits.' }}
                        </div>
                    </div>
                </div>

                {{-- Column 2 & 3: Menus --}}
                @if(isset($menu['footer-menu']))
                    @foreach($menu['footer-menu'] as $key => $val)
                        @if($key < 2)
                            @php
                                $name = $val['item']->languages->first()->pivot->name;
                            @endphp
                            <div class="col-lg-3 col-md-6">
                                <div class="footer-col">
                                    <h4 class="footer-heading mb-4 text-white fw-bold fs-6">{{ $name }}</h4>
                                    @if($val['children'])
                                        <ul class="list-unstyled footer-links">
                                            @foreach($val['children'] as $children)
                                                @php
                                                    $nameC = $children['item']->languages->first()->pivot->name;
                                                    $canonicalC = write_url($children['item']->languages->first()->pivot->canonical);
                                                @endphp
                                                <li class="mb-2">
                                                    <a href="{{ $canonicalC }}" class="text-decoration-none transition">
                                                        {{ $nameC }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

                {{-- Column 4: Contact (Pushed to end) --}}
                <div class="col-lg-3 col-md-6">
                    <div class="footer-col">
                        <h4 class="footer-heading mb-4 text-white fw-bold fs-6">Liên hệ</h4>
                        <div class="contact-item mb-4">
                            <div class="label text-secondary-light small text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Hotline</div>
                            <div class="value fs-5 fw-bold" style="color: #fff;">{{ $system['contact_hotline'] ?? '0969 919 000' }}</div>
                        </div>
                        <div class="contact-item mb-4">
                            <div class="label text-secondary-light small text-uppercase fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Email</div>
                            <div class="value fs-6" style="color: #fff;">{{ $system['contact_email'] ?? 'tshop@gmail.com' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-divider my-5 border-secondary opacity-10"></div>

        <div class="footer-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                {{-- Copyright --}}
                <div class="copyright-text small opacity-50 mb-4 mb-md-0">
                    {!! $system['homepage_copyright'] ?? '© 2025 T-shop. Crafted with passion for fine wine & spirits. All rights reserved.' !!}
                </div>

                {{-- Social Icons --}}
                <div class="social-links d-flex gap-3">
                    @php
                        $socials = [
                            'facebook' => ['icon' => 'fa-facebook-f', 'key' => 'social_facebook'],
                            'twitter' => ['icon' => 'fa-twitter', 'key' => 'social_twitter'],
                            'instagram' => ['icon' => 'fa-instagram', 'key' => 'social_instagram'],
                            'youtube' => ['icon' => 'fa-youtube', 'key' => 'social_youtube'],
                        ];
                    @endphp
                    @foreach($socials as $id => $social)
                        @if(isset($system[$social['key']]))
                            <a href="{{ $system[$social['key']] }}" class="social-icon-btn d-flex align-items-center justify-content-center transition rounded-circle">
                                <i class="fa {{ $social['icon'] }}"></i>
                            </a>
                        @else
                            <a href="#" class="social-icon-btn d-flex align-items-center justify-content-center transition rounded-circle">
                                <i class="fa {{ $social['icon'] }}"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</footer>

<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
    src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v5.0&appId=103609027035330&autoLogAppEvents=1">
</script>