@php
    $rating = (int) ($entry->rating ?? 0);
    $maxStars = 5;
@endphp

<span class="d-inline-flex align-items-center gap-1">
    @for ($i = 1; $i <= $maxStars; $i++)
        @if ($i <= $rating)
            <i class="la la-star text-warning"></i>
        @else
            <i class="la la-star text-muted"></i>
        @endif
    @endfor
    <small class="text-muted ml-1">({{ $rating }}/5)</small>
</span>
