@php
    $lang = $post->languages->first();
    $name = $lang->pivot->name;
    $canonical = write_url($lang->pivot->canonical);
@endphp
<div class="col-lg-3 col-md-6 mb-4">
    <div class="side-review-item">
        <a href="{{ $canonical }}" class="side-img-wrapper text-decoration-none">
            <img src="{{ $post->image }}" alt="{{ $name }}">
        </a>
        <a href="{{ $canonical }}" class="side-title d-block text-decoration-none mt-2">{{ $name }}</a>
    </div>
</div>
