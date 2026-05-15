<div class="row mb-2 align-items-end">

    <div class="col-md-6">
        <h1 class="page_heading">Pantry Expenses</h1>
    </div>

    <div class="col-md-6">

        <div class="d-flex justify-content-end">

            <a href="{{ route('pantry-expenses.create') }}"
               class="btn btn-primary mb-3"
               style="background-color:#6b51df;color:#fff;">

                Add Pantry Expense

            </a>

        </div>

    </div>

</div>


{{-- FILTERS --}}
<div class="row mb-3">

    <div class="col-md-3">

        <label>Date Range</label>

        <select id="pantryQuickFilter"
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
               id="pantryFromDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>To Date</label>

        <input type="date"
               id="pantryToDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>Title</label>

        <input type="text"
               id="pantryTitleFilter"
               class="form-control"
               placeholder="Item / Purpose">

    </div>

</div>


<div class="mb-3 text-end">

    <button type="button"
            id="pantryResetFilters"
            class="btn btn-secondary">

        Reset

    </button>

</div>


<div class="table-responsive">

    <table id="pantry-table"
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

    if ($.fn.DataTable.isDataTable('#pantry-table')) {

        $('#pantry-table').DataTable().destroy();
    }

    let table = $('#pantry-table').DataTable({

        processing: true,
        serverSide: true,

        stateSave: true,

        stateSaveCallback: function (settings, data) {

            localStorage.setItem(
                'pantry_table_state',
                JSON.stringify(data)
            );
        },

        stateLoadCallback: function () {

            return JSON.parse(
                localStorage.getItem('pantry_table_state')
            );
        },

        ajax: {

            url: "{{ route('pantry-expenses.index') }}",

            data: function (d) {

                d.quick = $('#pantryQuickFilter').val();

                d.from_date = $('#pantryFromDate').val();

                d.to_date = $('#pantryToDate').val();

                d.title = $('#pantryTitleFilter').val();
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


    $('#pantryQuickFilter, #pantryFromDate, #pantryToDate')
        .change(function () {

            table.draw();

        });


    let timer;

    $('#pantryTitleFilter').on('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(function () {

            table.draw();

        }, 500);

    });


    $('#pantryResetFilters').click(function () {

        $('#pantryQuickFilter').val('');

        $('#pantryFromDate').val('');

        $('#pantryToDate').val('');

        $('#pantryTitleFilter').val('');

        table.draw();

    });

});

</script>