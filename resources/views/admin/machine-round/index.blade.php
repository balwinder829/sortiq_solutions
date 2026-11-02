@extends('layouts.app')

@section('content')
<style>
    #machine-round-table th,
    #machine-round-table td { vertical-align: middle; }
    #machine-round-table td { text-transform: capitalize; }
    #machine-round-table td:nth-child(4) { text-transform: none; }
    .machine-actions {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .machine-move-btn {
        background-color: #6b51df; color: #fff; border: 1px solid #6b51df;
    }
    .machine-move-btn:hover { background-color: #5940c5; color: #fff; }
    #moveMachineModal .modal-header { background-color: #6b51df; color: #fff; }
    #moveMachineModal .btn-close { filter: brightness(0) invert(1); }
</style>

<div class="containe">

    {{-- Header --}}
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Machine Round</h1>
        </div>
        
    </div>
   

    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $class)
        @if(session($key))
            <div class="alert alert-{{ $class }} alert-dismissible fade show">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.machine-round.index') }}"
          id="machineFilterForm" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control machine-text-filter"
                   placeholder="Search name, email, mobile or college">
        </div>
        <div class="col-md-2">
            <select name="college_id" class="form-select machine-select-filter">
                <option value="">All Colleges</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}"
                        {{ (string)request('college_id') === (string)$college->id ? 'selected' : '' }}>
                        {{ $college->college_name ?? $college->full_name ?? $college->name ?? ('College #'.$college->id) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="class" class="form-select machine-select-filter">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class }}"
                        {{ request('class') === $class ? 'selected' : '' }}>
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn" style="background-color:#6b51df;color:#fff;">
                <i class="fa fa-filter"></i> Filter
            </button>
            <a href="{{ route('admin.machine-round.index') }}" class="btn btn-secondary">Reset</a>
            <a href="{{ route('admin.machine-round.export') . '?' . http_build_query(request()->query()) }}"
               class="btn btn-outline-success">
                <i class="fa fa-download"></i> Excel
            </a>
        </div>
    </form>

    <div class="machine-actions mb-3">
        <div>
            <h5 class="mb-1">Machine Round Students</h5>
            <span class="badge bg-primary">Total: {{ $machineStudents->count() }}</span>
            <span class="badge bg-secondary">Selected: <span id="machineSelectedCount">0</span></span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" id="moveMachineSelected" class="btn machine-move-btn">
                <i class="fa fa-share"></i> Move Selected
            </button>
            <button type="button" id="clearMachineSelection" class="btn btn-outline-secondary">
                Clear Selection
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.machine-round.move-final') }}" id="moveMachineStudentsForm">
        @csrf
        <input type="hidden" name="benefit_type" id="machineBenefitTypeInput">
        <div id="machineSelectedInputs"></div>

        <table id="machine-round-table" class="table table-bordered table-striped w-100">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox" id="selectAllMachine" aria-label="Select current page"></th>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>College</th>
                    <th>Source Test ID</th>
                    <!-- <th>Score</th> -->
                    <th>Moved To</th>
                    <th>Moved On</th>
                    <th>Destination ID</th>
                    <!-- <th>Notes</th> -->
                    <th>Added On</th>
                </tr>
            </thead>
            <tbody>
                @foreach($machineStudents as $machineRound)
                    <tr>
                        <td>
                            <input type="checkbox" class="machine-student-checkbox"
                                   value="{{ $machineRound->id }}"
                                   aria-label="Select {{ $machineRound->student_name }}">
                        </td>
                        <td>{{ $machineRound->id }}</td>
                        <td>{{ $machineRound->student_name }}</td>
                        <td>{{ $machineRound->student_email }}</td>
                        <td>{{ $machineRound->student_mobile ?: '-' }}</td>
                        <td>{{ $machineRound->college_name ?: '-' }}</td>
                        <td>{{ $machineRound->source_student_test_id }}</td>
                        <!-- <td>{{ $machineRound->score }}</td> -->
                        <td>
                            @if($machineRound->final_record_id)
                                <span class="badge bg-success">Final Students</span>
                            @else
                                <span class="badge bg-secondary">Not Moved</span>
                            @endif
                        </td>
                        <td>
                            {{ $machineRound->final_moved_at
                                ? \Carbon\Carbon::parse($machineRound->final_moved_at)->format('d M Y')
                                : '-' }}
                        </td>
                        <td>{{ $machineRound->final_record_id ? '#'.$machineRound->final_record_id : '-' }}</td>
                        <!-- <td>{{ $machineRound->notes ?: '-' }}</td> -->
                        <td>{{ $machineRound->created_at ? $machineRound->created_at->format('d M Y') : '-' }}</td>
                    </tr>
@endforeach
            </tbody>
        </table>
    </form>
</div>

<div class="modal fade" id="moveMachineModal" tabindex="-1"
     aria-labelledby="moveMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveMachineModalLabel">Move Selected Students to Final Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Select the internship category for
                    <strong><span id="machineModalSelectedCount">0</span></strong> selected student(s).</p>
                <label for="machineBenefitType" class="form-label">
                    Internship Category <span class="text-danger">*</span>
                </label>
                <select id="machineBenefitType" class="form-select">
                    <option value="">-- Choose Category --</option>
                    <option value="free">Free Internship</option>
                    <option value="discount">Discounted Internship</option>
                    <option value="stipend">Stipend-Based Internship</option>
                    <option value="3_month_ppo">3-Month Internship with PPO</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmMachineMove" class="btn machine-move-btn">
                    <i class="fa fa-check"></i> Continue
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const selectedIds = new Set();

    const table = $('#machine-round-table').DataTable({
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        paging: true,
        searching: false,
        info: true,
        scrollX: true,
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"i>>' +
             'rt' +
             '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        language: {
            info: 'Showing _START_ to _END_ of _TOTAL_ students',
            infoEmpty: 'Showing 0 to 0 students',
            lengthMenu: 'Show _MENU_ students per page',
            emptyTable: 'No Machine Round students found.',
            zeroRecords: 'No matching records found.'
        },
        columnDefs: [{ targets: 0, orderable: false, searchable: false }]
    });

    function updateSelectedCount() {
        $('#machineSelectedCount, #machineModalSelectedCount').text(selectedIds.size);
    }

    function syncMachineChecks() {
        const current = $(table.rows({ page: 'current' }).nodes()).find('.machine-student-checkbox');
        current.each(function () { this.checked = selectedIds.has(String(this.value)); });
        const checkedCount = current.filter(':checked').length;
        $('#selectAllMachine')
            .prop('checked', current.length > 0 && checkedCount === current.length)
            .prop('indeterminate', checkedCount > 0 && checkedCount < current.length);
        updateSelectedCount();
    }

    function renderSelectedInputs() {
        const container = $('#machineSelectedInputs').empty();
        selectedIds.forEach(function (id) {
            $('<input>', { type: 'hidden', name: 'machine_round_ids[]', value: id }).appendTo(container);
        });
    }

    function showMessage(options) {
        if (typeof Swal !== 'undefined') Swal.fire(options);
        else alert((options.title ? options.title + '\n' : '') + (options.text || ''));
    }

    $('#machine-round-table').on('change', '.machine-student-checkbox', function () {
        const id = String(this.value);
        if (this.checked) selectedIds.add(id);
        else selectedIds.delete(id);
        syncMachineChecks();
    });

    $('#selectAllMachine').on('change', function () {
        const checked = this.checked;
        $(table.rows({ page: 'current' }).nodes()).find('.machine-student-checkbox').each(function () {
            const id = String(this.value);
            this.checked = checked;
            if (checked) selectedIds.add(id);
            else selectedIds.delete(id);
        });
        syncMachineChecks();
    });

    table.on('draw', syncMachineChecks);
    syncMachineChecks();

    $('#clearMachineSelection').on('click', function () {
        selectedIds.clear();
        syncMachineChecks();
    });

    $('#moveMachineSelected').on('click', function () {
        if (selectedIds.size === 0) {
            showMessage({
                icon: 'warning', title: 'No Students Selected',
                text: 'Please select at least one student.',
                confirmButtonText: 'OK', reverseButtons: false
            });
            return;
        }
        $('#machineBenefitType').val('');
        $('#machineModalSelectedCount').text(selectedIds.size);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('moveMachineModal')).show();
    });

    $('#confirmMachineMove').on('click', function () {
        const benefitType = $('#machineBenefitType').val();
        if (!benefitType) {
            showMessage({
                icon: 'warning', title: 'Category Required',
                text: 'Please select an internship category.',
                confirmButtonText: 'OK', reverseButtons: false
            });
            return;
        }

        const confirmMove = function () {
            $('#machineBenefitTypeInput').val(benefitType);
            renderSelectedInputs();
            $('#confirmMachineMove').prop('disabled', true);
            $('#moveMachineStudentsForm')[0].submit();
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'question', title: 'Confirm Movement',
                text: 'Move ' + selectedIds.size + ' selected student(s) to Final Students?',
                showCancelButton: true, confirmButtonText: 'Yes, Move',
                cancelButtonText: 'Cancel', reverseButtons: false
            }).then(function (result) {
                if (result.isConfirmed) confirmMove();
            });
        } else if (confirm('Move selected students to Final Students?')) {
            confirmMove();
        }
    });

    let searchTimer;
    $('.machine-text-filter').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            $('#machineFilterForm').trigger('submit');
        }, 400);
    });
    $('.machine-select-filter').on('change', function () {
        $('#machineFilterForm').trigger('submit');
    });
});
</script>
@endpush
