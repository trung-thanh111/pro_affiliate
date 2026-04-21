@extends('frontend.homepage.layout')
@section('content')
<div class="redirect-container">
    <div class="uk-container uk-container-center">
        <div class="redirect-card">
            <div class="redirect-logo">
                <img src="{{ image($system['homepage_logo'] ?? '') }}" alt="Logo">
            </div>
            
            <div class="redirect-content">
                <p class="redirect-text">Bạn đang được chuyển đến trang bán sản phẩm</p>
                <h1 class="product-name">"{{ $product->name ?? '' }}"</h1>
                <p class="provider-text">tại <span>{{ parse_url($product->link, PHP_URL_HOST) }}</span></p>
            </div>

            <div class="redirect-timer">
                <div class="timer-circle">
                    <svg viewBox="0 0 36 36" class="circular-chart">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="circle" stroke-dasharray="100, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="timer-number">3s</div>
                </div>
            </div>

            <div class="redirect-actions">
                <p>hoặc</p>
                <a href="{{ $product->link }}" class="btn-redirect-now">Chuyển đến ngay lập tức</a>
                <a href="{{ url()->previous() }}" class="btn-back">Quay lại trang cũ</a>
            </div>
        </div>
    </div>
</div>

<style>
    .redirect-container {
        padding: 80px 0;
        background: #f4f7f6;
        min-height: 80vh;
        display: flex;
        align-items: center;
    }
    .redirect-card {
        max-width: 600px;
        margin: 0 auto;
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        text-align: center;
    }
    .redirect-logo img {
        height: 50px;
        margin-bottom: 30px;
    }
    .product-name {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin: 15px 0;
    }
    .provider-text span {
        color: #ee4d2d;
        font-weight: 600;
    }
    .redirect-timer {
        margin: 30px 0;
    }
    .timer-circle {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }
    .timer-number {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 24px;
        font-weight: 700;
        color: #2b6777;
    }
    .circular-chart {
        display: block;
        margin: 10px auto;
        max-width: 100%;
        max-height: 250px;
    }
    .circle-bg {
        fill: none;
        stroke: #eee;
        stroke-width: 2.8;
    }
    .circle {
        fill: none;
        stroke-width: 2.8;
        stroke-linecap: round;
        stroke: #2b6777;
        transition: stroke-dasharray 1s linear;
    }
    .btn-redirect-now {
        display: inline-block;
        background: #ee4d2d;
        color: #fff !important;
        padding: 12px 30px;
        border-radius: 6px;
        font-weight: 600;
        margin: 20px 0 10px;
        text-decoration: none;
        transition: all 0.3s;
    }
    .btn-redirect-now:hover {
        background: #d73211;
        transform: translateY(-2px);
    }
    .btn-back {
        display: block;
        color: #888;
        font-size: 14px;
        text-decoration: underline;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timeLeft = 3;
        const timerNumber = document.querySelector('.timer-number');
        const circle = document.querySelector('.circle');
        const targetUrl = "{{ $product->link }}";

        const timer = setInterval(function() {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(timer);
                window.location.href = targetUrl;
            }
            timerNumber.innerText = timeLeft + 's';
            
            // Cập nhật vòng tròn (giảm dần)
            let percent = (timeLeft / 3) * 100;
            circle.setAttribute('stroke-dasharray', percent + ', 100');
        }, 1000);
    });
</script>
@endsection
