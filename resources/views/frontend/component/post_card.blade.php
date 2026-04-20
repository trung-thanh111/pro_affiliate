@php
    $title = $post->languages->first()->pivot->name ?? '';
    $description = $post->languages->first()->pivot->description ?? '';
    $image = $post->image ?? '';
    $canonical = $post->languages->first()->pivot->canonical ?? '';
    $href = write_url($canonical);
    $date = convertDateTime($post->created_at, 'd/m/Y');
@endphp

<div class="post-card-premium bg-white h-100 transition-up shadow-sm">
    <div class="card-thumb position-relative overflow-hidden" style="height: 180px;">
        <a href="{{ $href }}">
            <img src="{{ $image }}" alt="{{ $title }}" class="w-100 h-100 object-fit-cover">
        </a>
    </div>
    <div class="card-body p-3">
        <div class="card-meta d-flex align-items-center gap-3 mb-2 small text-secondary">
            <span class="d-flex align-items-center gap-1">
                <i class="bi bi-calendar3"></i> {{ $date }}
            </span>
        </div>
        <h5 class="card-title fw-bold">
            <a href="{{ $href }}" class="text-decoration-none text-dark stretched-link">
                {{ Str::limit($title, 60) }}
            </a>
        </h5>
        @if(!empty($description))
            <p class="card-desc mb-0">
                {{ Str::limit(strip_tags($description), 80) }}
            </p>
        @endif
    </div>
</div>
