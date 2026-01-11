<form method="GET"
    class="d-flex align-items-center gap-1"
    style="margin-right:8px; font-size:12px;">

    <span class="text-muted fw-bold me-1">Enquiry Date:</span>

    <input type="date"
        name="from"
        value="{{ request('from') }}"
        class="form-control form-control-sm"
        style="max-width:130px;"
        required>

    <span class="text-muted">–</span>

    <input type="date"
        name="to"
        value="{{ request('to') }}"
        class="form-control form-control-sm"
        style="max-width:130px;"
        required>

    <button type="submit"
        class="btn btn-sm btn-primary px-2">
        Filter
    </button>
</form>