@php
    $field['name'] = $field['name'] ?? 'images';
@endphp

<div class="form-group">
    <label>{!! $field['label'] ?? 'Product Images' !!}</label>

    <input
        type="file"
        name="{{ $field['name'] }}[]"
        multiple
        class="form-control"
        accept="image/*"
    >

    @if (!empty($field['hint']))
        <small class="form-text text-muted">
            {{ $field['hint'] }}
        </small>
    @endif
</div>
