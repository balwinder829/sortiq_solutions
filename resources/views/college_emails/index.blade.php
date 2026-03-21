@extends('layouts.app')

@section('content')
<div class="container">

<div class="row mb-2 align-items-end">

    <div class="col-md-8">
        <h1 class="page_heading">Emails</h1>
    </div>

    <div class="col-md-4">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.college-emails.create') }}"
               style="background-color: #6b51df; color: #fff;"
               class="btn btn-primary mb-3">
                Send Email
            </a>
        </div>
    </div>

</div>

{{-- FILTERS --}}
<div class="col-md-12 mb-3">
    <form class="row g-2 align-items-end">

        <div class="col-md-2">
            <select name="college_id" class="form-select">
                <option value="">College</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}">
                        {{ $college->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Status</option>
                <option value="sent">Sent</option>
                <option value="failed">Failed</option>
                <option value="pending">Pending</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="type" class="form-select">
                <option value="">Type</option>
                <option value="hod">HOD</option>
                <option value="tpo">TPO</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control">
        </div>

        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control">
        </div>

        <div class="col-md-2">
             <a href="{{ route('admin.college-emails.index') }}" class="btn btn-secondary">
                    Reset
                </a>
        </div>

    </form>
</div>

<div class="table-responsive">
    <table id="emails-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>College</th>
                <th>Email</th>
                <th>Type</th>
                <th>Status</th>
                <th>Sent At</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

</div>
@endsection

@push('scripts')

<script>
$(document).ready(function () {

    var table = $('#emails-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.college-emails.index') }}",
            data: function (d) {
                d.college_id = $('select[name=college_id]').val();
                d.status = $('select[name=status]').val();
                d.type = $('select[name=type]').val();
                d.date_from = $('input[name=date_from]').val();
                d.date_to = $('input[name=date_to]').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'college_name' },
            { data: 'email' },
            { data: 'type' },
            { data: 'status' },
            { data: 'sent_at' }
        ]
    });

    $('select, input').on('change', function () {
        table.ajax.reload();
    });

});
</script>

@endpush