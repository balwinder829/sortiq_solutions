<div class="row mb-2 align-items-end">

    <div class="col-md-6">
        <h1 class="page_heading">Cleaning Expenses</h1>
    </div>

    <div class="col-md-6">

        <div class="d-flex justify-content-end">

            <a href="{{ route('office-cleaning-expenses.create') }}"
               class="btn btn-primary mb-3"
               style="background-color:#6b51df;color:#fff;">

                Add Expense

            </a>

        </div>

    </div>

</div>


{{-- FILTERS --}}
<div class="row mb-3">

    <div class="col-md-3">

        <label>Date Range</label>

        <select id="cleaningQuickFilter"
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
               id="cleaningFromDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>To Date</label>

        <input type="date"
               id="cleaningToDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>Title</label>

        <input type="text"
               id="cleaningTitleFilter"
               class="form-control"
               placeholder="Item / Purpose">

    </div>

</div>


<div class="mb-3 text-end">

    <button type="button"
            id="cleaningResetFilters"
            class="btn btn-secondary">

        Reset

    </button>

</div>


<div class="table-responsive">

    <table id="office-cleaning-table"
           class="table table-bordered table-striped">

        <thead>

            <tr>

                <th>ID</th>
                <th>Date</th>
                <th>Title</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Other Charges</th>
                <th>Actions</th>

            </tr>

        </thead>

    </table>

</div>


<script>

$(document).ready(function () {

    if ($.fn.DataTable.isDataTable('#office-cleaning-table')) {

        $('#office-cleaning-table').DataTable().destroy();
    }

    let table = $('#office-cleaning-table').DataTable({

        processing: true,
        serverSide: true,

        stateSave: true,

        stateSaveCallback: function (settings, data) {

            localStorage.setItem(
                'office_cleaning_table_state',
                JSON.stringify(data)
            );
        },

        stateLoadCallback: function () {

            return JSON.parse(
                localStorage.getItem('office_cleaning_table_state')
            );
        },

        ajax: {

            url: "{{ route('office-cleaning-expenses.index') }}",

            data: function (d) {

                d.quick = $('#cleaningQuickFilter').val();

                d.from_date = $('#cleaningFromDate').val();

                d.to_date = $('#cleaningToDate').val();

                d.title = $('#cleaningTitleFilter').val();
            }
        },

        columns: [

            { data: 0 },

            { data: 1 },

            { data: 2 },

            { data: 3 },

            { data: 4 },

            { data: 5 },

            { data: 6 }

        ]
    });


    $('#cleaningQuickFilter, #cleaningFromDate, #cleaningToDate')
        .change(function () {

            table.draw();

        });


    let timer;

    $('#cleaningTitleFilter').on('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(function () {

            table.draw();

        }, 500);

    });


    $('#cleaningResetFilters').click(function () {

        $('#cleaningQuickFilter').val('');

        $('#cleaningFromDate').val('');

        $('#cleaningToDate').val('');

        $('#cleaningTitleFilter').val('');

        table.draw();

    });

});

</script>