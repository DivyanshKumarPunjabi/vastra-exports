@php
    $categories = \App\Models\ProductCategory::pluck('name', 'id');
@endphp

<form method="GET" action="{{ url()->current() }}" class="d-flex gap-2 mb-2">

    <input type="text"
           name="title"
           value="{{ request('title') }}"
           class="form-control"
           placeholder="Product Title">

    <select name="category_id" class="form-control">
        <option value="">All Categories</option>
        @foreach($categories as $id => $name)
            <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>

    <input type="text"
           name="fabric"
           value="{{ request('fabric') }}"
           class="form-control"
           placeholder="Fabric">

    <input type="text"
           name="style_code"
           value="{{ request('style_code') }}"
           class="form-control"
           placeholder="Style Code">

    <button type="submit" class="btn btn-primary">
        Filter
    </button>
</form>
