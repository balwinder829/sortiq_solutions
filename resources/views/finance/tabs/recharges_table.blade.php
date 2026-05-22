<div class="row mb-2 align-items-end">

    <div class="col-md-6">
        <h1 class="page_heading">Recharges</h1>
    </div>

    <div class="col-md-6">

        <div class="d-flex justify-content-end">

            <a href="{{ route('recharges.create') }}"
               class="btn mb-3"
               style="background-color:#6b51df;color:#fff;">

                Add Recharge

            </a>

        </div>

    </div>

</div>


{{-- FILTERS --}}
<div class="row mb-3">

    <div class="col-md-6">

        <label>Search</label>

        <input type="text"
               id="rechargeSearch"
               class="form-control"
               placeholder="Mobile / Employee / Reference">

    </div>

    <div class="col-md-3">

        <label>Status</label>

        <select id="rechargeStatus"
                class="form-control">

            <option value="">All</option>

            <option value="pending">Pending</option>

            <option value="completed">Completed</option>

            <option value="failed">Failed</option>

            <option value="refunded">Refunded</option>

        </select>

    </div>

    <div class="col-md-3 d-flex align-items-end">

        <button type="button"
                id="rechargeResetFilters"
                class="btn btn-secondary w-100">

            Reset

        </button>

    </div>

</div>


<div class="table-responsive">

    <table id="recharges-table"
           class="table table-bordered table-striped">

        <thead>

            <tr>

                <th>#</th>
                <th>Mobile Number</th>
                <th>Employee Name</th>
                <th>Operator</th>
                <th>Amount</th>
                <th>Recharge Date</th>
                <th>Actions</th>

            </tr>

        </thead>

    </table>

</div>


<script>

$(document).ready(function () {

    if ($.fn.DataTable.isDataTable('#recharges-table')) {

        $('#recharges-table').DataTable().destroy();
    }

    let table = $('#recharges-table').DataTable({

        processing: true,
        serverSide: true,

        stateSave: true,

        stateSaveCallback: function (settings, data) {

            localStorage.setItem(
                'recharges_table_state',
                JSON.stringify(data)
            );
        },

        stateLoadCallback: function () {

            return JSON.parse(
                localStorage.getItem('recharges_table_state')
            );
        },

        ajax: {

            url: "{{ route('recharges.index') }}",

            data: function (d) {

                d.q = $('#rechargeSearch').val();

                d.status = $('#rechargeStatus').val();
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


    $('#rechargeStatus').change(function () {

        table.draw();

    });


    let timer;

    $('#rechargeSearch').on('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(function () {

            table.draw();

        }, 500);

    });


    $('#rechargeResetFilters').click(function () {

        $('#rechargeSearch').val('');

        $('#rechargeStatus').val('');

        table.draw();

    });

});

</script>