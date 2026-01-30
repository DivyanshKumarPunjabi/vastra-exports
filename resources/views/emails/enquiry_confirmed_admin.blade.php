<h3>{{ $heading1 }}</h3>

<p>{!! nl2br(e($content)) !!}</p>

@if($content_footnote)
    <p><small>{{ $content_footnote }}</small></p>
@endif
