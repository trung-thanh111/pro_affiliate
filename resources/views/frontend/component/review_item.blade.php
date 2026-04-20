@php
    $lang = $post->languages->first();
    $name = $lang->pivot->name;
    $desc = $lang->pivot->description;
    $canonical = write_url($lang->pivot->canonical);
    $date = date('d/m/Y', strtotime($post->created_at));
@endphp
<div class="review-item-horizontal mb-4">
    <a href="{{ $canonical }}" class="d-flex flex-column flex-md-row gap-3 gap-md-4 text-decoration-none">
        <div class="review-img-wrapper flex-shrink-0">
            <img src="{{ $post->image }}" alt="{{ $name }}" class="rounded-3 object-fit-cover">
        </div>
        <div class="review-content">
            <h3 class="review-title mb-2">{{ $name }}</h3>
            <div class="review-desc text-secondary mb-3">
                {{ Str::limit(strip_tags($desc), 250) }}
            </div>
            <div class="review-meta d-flex align-items-center gap-2 small">
                <i class="bi bi-calendar3"></i>
                <span>{{ $date }}</span>
            </div>
        </div>
    </a>
</div>
