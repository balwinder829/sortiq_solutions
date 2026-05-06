<div class="row mb-2 align-items-end">

    <div class="col-md-4">
        <h1 class="page_heading">Districts</h1>
    </div>

    <div class="col-md-4">
        <label>Filter by State</label>
        <select id="stateFilter" class="form-control">
            <option value="">All States</option>
            @foreach($states as $state)
                <option value="{{ $state->id }}">{{ $state->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <div class="d-flex justify-content-end">
            <a href="{{ route('districts.create', ['tab' => 'districts']) }}"
               class="btn btn-primary mb-3"
               style="background-color:#6b51df;color:#fff;">
                Add District
            </a>
        </div>
    </div>

</div>

<div class="table-responsive">
    <table id="districts-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>State</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

<script>
$(document).ready(function () {

    if ($.fn.DataTable.isDataTable('#districts-table')) {
        $('#districts-table').DataTable().destroy();
    }

    var table = $('#districts-table').DataTable({
        processing: true,
        serverSide: true,

        stateSave: true,
        stateSaveCallback: function (settings, data) {
            localStorage.setItem('districts_table_state', JSON.stringify(data));
        },
        stateLoadCallback: function () {
            return JSON.parse(localStorage.getItem('districts_table_state'));
        },

        ajax: {
            url: "{{ route('districts.data') }}",
            data: function (d) {
                d.state_id = $('#stateFilter').val();
            }
        },

        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 }
        ]
    });

    $('#stateFilter').change(function () {
        table.page('first').draw(false);
    });

});
</script>