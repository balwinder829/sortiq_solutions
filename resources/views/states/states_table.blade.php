<div class="row mb-2 align-items-end">

    <div class="col-md-8">
        <h1 class="page_heading">States</h1>
    </div>

    <div class="col-md-4">
        <div class="d-flex justify-content-end">
            <a href="{{ route('states.create', ['tab' => 'states']) }}"
               class="btn btn-primary mb-3"
               style="background-color:#6b51df;color:#fff;">
                Add State
            </a>
        </div>
    </div>

</div>

<div class="table-responsive">
    <table id="states-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Code</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function () {

    if ($.fn.DataTable.isDataTable('#states-table')) {
        $('#states-table').DataTable().destroy();
    }

    $('#states-table').DataTable({
        processing: true,
        serverSide: true,

        stateSave: true,
        stateSaveCallback: function (settings, data) {
            localStorage.setItem('states_table_state', JSON.stringify(data));
        },
        stateLoadCallback: function () {
            return JSON.parse(localStorage.getItem('states_table_state'));
        },

        ajax: "{{ route('states.data') }}",

        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 }
        ]
    });

});
</script>