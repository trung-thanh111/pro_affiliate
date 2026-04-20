@extends('frontend.homepage.layout')
@section('content')
    <section class="contact-wrapper">
     @if (!empty($slides['main-slide']) && is_array($slides['main-slide']))
        <section class="main-slide">
            @foreach($slides['main-slide']['item'] as $key => $val )
            @if($key > 0) @break @endif
            <span href="{{ $val['canonical'] }}" title="" class="image img-cover wow fadeInRight" data-wow-delay="0.8s">
                <img src="{{ $val['image'] }}" alt="{{ $val['name'] }}">
            </span>
            @endforeach

            <div class="contact-form-1">
                <div class="uk-grid uk-grid-medium">
                    {{-- Thông tin liên hệ --}}
                    <div class="uk-width-small-1-1 uk-width-large-1-2">
                        <div class="contact-infor">
                            <h2 class="heading-4"><span>Văn Phòng</span></h2>
                            <p>Địa chỉ: {{ $system['contact_address'] ?? '' }}</p>
                            <p>Hotline: {{ $system['contact_hotline'] ?? '' }}</p>
                            <p>Mail: {{ $system['contact_email'] ?? '' }}</p>

                            <a href="{{ $system['contact_map'] ?? '#' }}" title="View us on the map" class="view-map">
                                View us on the map
                            </a>

                            <a href="tel:{{ $system['contact_hotline'] ?? '' }}" class="hotline">
                                <div class="label">Hotline</div>
                                <div class="number">{{ $system['contact_hotline'] ?? '' }}</div>
                            </a>
                        </div>
                    </div>

                    {{-- Form phản hồi --}}
                    <div class="uk-width-small-1-1 uk-width-large-1-2">
                        <div class="form-contact-1">
                            <h2 class="heading-4"><span>Ý kiến phản hồi</span></h2>

                            <form method="post" class="form uk-form">
                                @csrf

                                @if ($errors->any())
                                    <div class="callout callout-danger" 
                                         style="padding:10px;background:rgb(195,94,94);color:#fff;margin-bottom:10px;">
                                        @foreach ($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="form-row mb10">
                                    <input type="text" name="fullname" class="input-text" placeholder="Họ và Tên"
                                           value="{{ old('fullname') }}">
                                </div>

                                <div class="form-row mb10">
                                    <input type="text" name="phone" class="input-text" placeholder="Số điện thoại"
                                           value="{{ old('phone') }}">
                                </div>

                                <div class="form-row mb10">
                                    <input type="email" name="email" class="input-text" placeholder="Email"
                                           value="{{ old('email') }}">
                                </div>

                                <div class="form-row mb10">
                                    <textarea name="message" cols="30" rows="10" class="textarea"
                                              placeholder="Lời nhắn">{{ old('message') }}</textarea>
                                </div>

                                <div class="form-row mb10">
                                    <input type="submit" value="Gửi" class="btn-submit">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</section>

@endsection

