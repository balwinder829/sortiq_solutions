@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-3 align-items-end">

        <div class="col-md-6">
            <h1 class="page_heading">Workshop Expenses</h1>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('workshop-expenses.create') }}"
               class="btn btn-primary">
                Add Workshop Expense
            </a>
        </div>

    </div>

    <div class="row mb-3">

        <div class="col-md-4">
            <select name="workshop_id" class="form-select select2">
                <option value="">Workshop</option>

                @foreach($workshops as $workshop)
                    <option value="{{ $workshop->id }}">
                        {{ $workshop->title }}
                        -
                        {{ $workshop->college->FullName ?? '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <input type="text"
                   name="title"
                   class="form-control"
                   placeholder="Expense Title">
        </div>

        <div class="col-md-3">
            <input type="date"
                   name="date"
                   class="form-control">
        </div>

        <div class="col-md-2">
            <a href="{{ route('workshop-expenses.index') }}"
               class="btn btn-secondary w-100">
                Reset
            </a>
        </div>

    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="expense-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Workshop</th>
                    <th>College</th>
                    <th>Title</th>
                    <th>Travel Expense</th>
                    <th>Other Expense</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th width="100">Actions</th>
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

    var table = $('#expense-table').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: "{{ route('workshop-expenses.data') }}",
            data: function (d) {

                d.workshop_id = $('select[name=workshop_id]').val();
                d.title       = $('input[name=title]').val();
                d.date        = $('input[name=date]').val();

            }
        },

        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8, orderable:false, searchable:false },
        ],

        pageLength: 50,
        lengthMenu: [5,10,25,50,100]
    });

    $('select[name=workshop_id]').on('change', function () {
        table.ajax.reload();
    });

    $('input[name=title], input[name=date]').on('keyup change', function () {
        table.ajax.reload();
    });

});
</script>

@endpush