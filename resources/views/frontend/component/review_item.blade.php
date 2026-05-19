@php
    $lang = $post->languages->first();
    $name = $lang->pivot->name;
    $canonical = write_url($lang->pivot->canonical);
    $redirectUrl = get_post_affiliate_url($post);
@endphp
<div class="col-lg-3 col-md-6 mb-4" data-redirect="{{ $redirectUrl }}">
    <div class="side-review-item">
        <a href="{{ $canonical }}" class="side-img-wrapper text-decoration-none post-link-redirect" data-redirect="{{ $redirectUrl }}">
            <img src="{{ $post->image }}" alt="{{ $name }}">
        </a>
        <a href="{{ $canonical }}" class="side-title d-block text-decoration-none mt-2 post-link-redirect" data-redirect="{{ $redirectUrl }}">{{ $name }}</a>
    </div>
</div>
