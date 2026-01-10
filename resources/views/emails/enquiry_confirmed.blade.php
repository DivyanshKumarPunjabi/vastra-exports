<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

@if(!empty($white_logo))
    <img src="{{ $white_logo }}" alt="Vastra" height="60">
@endif

<p>Dear {{ $first_name ?? 'Customer' }},</p>

<p>
    Thank you for reaching out to <strong>Vastra</strong>. We appreciate your interest in our garment manufacturing and export services.
</p>

<p>
    {!! nl2br(e($content)) !!}
</p>

@if(!empty($content_footnote))
    <p>{{ $content_footnote }}</p>
@endif

<p>
    Warm regards,<br>
    <strong>Team Vastra</strong><br>
    Garment Manufacturing & Private Label Solutions
</p>

</body>
</html>
