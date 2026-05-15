<div class="row mb-2 align-items-end">

    <div class="col-md-6">
        <h1 class="page_heading">Office Paper Expenses</h1>
    </div>

    <div class="col-md-6">

        <div class="d-flex justify-content-end">

            <a href="{{ route('office-paper-expenses.create') }}"
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

        <select id="paperQuickFilter"
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
               id="paperFromDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>To Date</label>

        <input type="date"
               id="paperToDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>Title</label>

        <input type="text"
               id="paperTitleFilter"
               class="form-control"
               placeholder="Item / Purpose">

    </div>

</div>


<div class="mb-3 text-end">

    <button type="button"
            id="paperResetFilters"
            class="btn btn-secondary">

        Reset

    </button>

</div>


<div class="table-responsive">

    <table id="office-paper-table"
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

    if ($.fn.DataTable.isDataTable('#office-paper-table')) {

        $('#office-paper-table').DataTable().destroy();
    }

    let table = $('#office-paper-table').DataTable({

        processing: true,
        serverSide: true,

        stateSave: true,

        stateSaveCallback: function (settings, data) {

            localStorage.setItem(
                'office_paper_table_state',
                JSON.stringify(data)
            );
        },

        stateLoadCallback: function () {

            return JSON.parse(
                localStorage.getItem('office_paper_table_state')
            );
        },

        ajax: {

            url: "{{ route('office-paper-expenses.index') }}",

            data: function (d) {

                d.quick = $('#paperQuickFilter').val();

                d.from_date = $('#paperFromDate').val();

                d.to_date = $('#paperToDate').val();

                d.title = $('#paperTitleFilter').val();
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


    $('#paperQuickFilter, #paperFromDate, #paperToDate')
        .change(function () {

            table.draw();

        });


    let timer;

    $('#paperTitleFilter').on('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(function () {

            table.draw();

        }, 500);

    });


    $('#paperResetFilters').click(function () {

        $('#paperQuickFilter').val('');

        $('#paperFromDate').val('');

        $('#paperToDate').val('');

        $('#paperTitleFilter').val('');

        table.draw();

    });

});

</script>