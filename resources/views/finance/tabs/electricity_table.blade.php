<div class="row mb-2 align-items-end">

    <div class="col-md-6">
        <h1 class="page_heading">Electricity Bills</h1>
    </div>

    <div class="col-md-6">

        <div class="d-flex justify-content-end">

            <a href="{{ route('office-expenses.create') }}"
               class="btn btn-primary mb-3"
               style="background-color:#6b51df;color:#fff;">

                Add Electricity Bill

            </a>

        </div>

    </div>

</div>


{{-- FILTERS --}}
<div class="row mb-3">

    <div class="col-md-3">

        <label>Date Range</label>

        <select id="quickFilter"
                class="form-control">

            <option value="">All</option>

            <option value="today">Today</option>

            <option value="yesterday">Yesterday</option>

            <option value="7days">Last 7 Days</option>

            <option value="1month">Last 1 Month</option>

        </select>

    </div>

    <div class="col-md-3">

        <label>From Date</label>

        <input type="date"
               id="fromDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>To Date</label>

        <input type="date"
               id="toDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>Title</label>

        <input type="text"
               id="titleFilter"
               class="form-control"
               placeholder="Expense title">

    </div>

</div>


<div class="mb-3 text-end">

    <button type="button"
            id="resetFilters"
            class="btn btn-secondary">

        Reset

    </button>

</div>


<div class="table-responsive">

    <table id="electricity-table"
           class="table table-bordered table-striped">

        <thead>

            <tr>

                <th>ID</th>
                <th>Date</th>
                <th>Title</th>
                <th>Amount</th>
                <th>Actions</th>

            </tr>

        </thead>

    </table>

</div>


<script>

$(document).ready(function () {

    if ($.fn.DataTable.isDataTable('#electricity-table')) {

        $('#electricity-table').DataTable().destroy();
    }

    let table = $('#electricity-table').DataTable({

        processing: true,
        serverSide: true,

        stateSave: true,

        stateSaveCallback: function (settings, data) {

            localStorage.setItem(
                'electricity_table_state',
                JSON.stringify(data)
            );
        },

        stateLoadCallback: function () {

            return JSON.parse(
                localStorage.getItem('electricity_table_state')
            );
        },

        ajax: {

            url: "{{ route('office-expenses.index') }}",

            data: function (d) {

                d.quick = $('#quickFilter').val();

                d.from_date = $('#fromDate').val();

                d.to_date = $('#toDate').val();

                d.title = $('#titleFilter').val();
            }
        },

        columns: [

            { data: 0 },

            { data: 1 },

            { data: 2 },

            { data: 3 },

            { data: 4 }

        ]
    });


    // Filter Change
    $('#quickFilter, #fromDate, #toDate')
        .change(function () {

            table.draw();

        });


    // Title Search
    let timer;

    $('#titleFilter').on('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(function () {

            table.draw();

        }, 500);

    });


    // Reset
    $('#resetFilters').click(function () {

        $('#quickFilter').val('');

        $('#fromDate').val('');

        $('#toDate').val('');

        $('#titleFilter').val('');

        table.draw();

    });

});

</script>