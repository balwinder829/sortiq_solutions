<div class="row mb-2 align-items-end">

    <div class="col-md-6">
        <h1 class="page_heading">Office Assets</h1>
    </div>

    <div class="col-md-6">

        <div class="d-flex justify-content-end">

            <a href="{{ route('office-assets.create') }}"
               class="btn btn-primary mb-3"
               style="background-color:#6b51df;color:#fff;">

                Add Office Asset

            </a>

        </div>

    </div>

</div>


{{-- FILTERS --}}
<div class="row mb-3">

    <div class="col-md-3">

        <label>Date Range</label>

        <select id="assetQuickFilter"
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
               id="assetFromDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>To Date</label>

        <input type="date"
               id="assetToDate"
               class="form-control">

    </div>

    <div class="col-md-3">

        <label>Title</label>

        <input type="text"
               id="assetTitleFilter"
               class="form-control"
               placeholder="Asset name">

    </div>

</div>


<div class="mb-3 text-end">

    <button type="button"
            id="assetResetFilters"
            class="btn btn-secondary">

        Reset

    </button>

</div>


<div class="table-responsive">

    <table id="office-assets-table"
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

    if ($.fn.DataTable.isDataTable('#office-assets-table')) {

        $('#office-assets-table').DataTable().destroy();
    }

    let table = $('#office-assets-table').DataTable({

        processing: true,
        serverSide: true,

        stateSave: true,

        stateSaveCallback: function (settings, data) {

            localStorage.setItem(
                'office_assets_table_state',
                JSON.stringify(data)
            );
        },

        stateLoadCallback: function () {

            return JSON.parse(
                localStorage.getItem('office_assets_table_state')
            );
        },

        ajax: {

            url: "{{ route('office-assets.index') }}",

            data: function (d) {

                d.quick = $('#assetQuickFilter').val();

                d.from_date = $('#assetFromDate').val();

                d.to_date = $('#assetToDate').val();

                d.title = $('#assetTitleFilter').val();
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


    $('#assetQuickFilter, #assetFromDate, #assetToDate')
        .change(function () {

            table.draw();

        });


    let timer;

    $('#assetTitleFilter').on('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(function () {

            table.draw();

        }, 500);

    });


    $('#assetResetFilters').click(function () {

        $('#assetQuickFilter').val('');

        $('#assetFromDate').val('');

        $('#assetToDate').val('');

        $('#assetTitleFilter').val('');

        table.draw();

    });

});

</script>