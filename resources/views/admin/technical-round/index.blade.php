@extends('layouts.app')

@section('content')
<style>
    #technical-round-table th,
    #technical-round-table td {
        vertical-align: middle;
    }

    #technical-round-table td {
        text-transform: capitalize;
    }

    #technical-round-table td:nth-child(4) {
        text-transform: none;
    }

    .technical-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .technical-move-btn {
        background-color: #6b51df;
        color: #fff;
        border: 1px solid #6b51df;
    }

    .technical-move-btn:hover {
        background-color: #5940c5;
        color: #fff;
    }

    #moveTechnicalModal .modal-header {
        background-color: #6b51df;
        color: #fff;
    }

    #moveTechnicalModal .btn-close {
        filter: brightness(0) invert(1);
    }
</style>

<div class="container">

    {{-- Header --}}
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Technical Round</h1>
        </div>
        
    </div>
     

    {{-- Flash messages --}}
    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $class)
        @if(session($key))
            <div class="alert alert-{{ $class }} alert-dismissible fade show">
                {{ session($key) }}
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET"
          action="{{ route('admin.technical-round.index') }}"
          id="technicalFilterForm"
          class="row g-2 mb-3">

        <div class="col-md-3">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control technical-text-filter"
                   placeholder="Search name, email, mobile or college">
        </div>

        <div class="col-md-3">
            <select name="college_id"
                    class="form-select technical-select-filter">
                <option value="">All Colleges</option>

                @foreach($colleges as $college)
                    <option value="{{ $college->id }}"
                        {{ (string) request('college_id') === (string) $college->id ? 'selected' : '' }}>
                        {{ $college->college_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="class"
                    class="form-select technical-select-filter">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class }}"
                        {{ request('class') === $class ? 'selected' : '' }}>
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- <div class="col-md-2">
            <select name="status"
                    class="form-select technical-select-filter">
                <option value="">All Statuses</option>

                @foreach($statuses as $status)
                    <option value="{{ $status }}"
                        {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div> -->

        <div class="col-md-3 d-flex gap-2">
            <button type="submit"
                    class="btn"
                    style="background-color:#6b51df;color:#fff;">
                <i class="fa fa-filter"></i> Filter
            </button>

            <a href="{{ route('admin.technical-round.index') }}"
               class="btn btn-secondary">
                Reset
            </a>

            <a href="{{ route('admin.technical-round.export') . '?' . http_build_query(request()->query()) }}"
               class="btn btn-outline-success">
                <i class="fa fa-download"></i> Excel
            </a>
        </div>
    </form>

    {{-- Table heading and actions --}}
    <div class="technical-actions mb-3">
        <div>
            <h5 class="mb-1">Technical Round Students</h5>

            <span class="badge bg-primary">
                Total: {{ $technicalStudents->count() }}
            </span>

            <span class="badge bg-secondary">
                Selected: <span id="technicalSelectedCount">0</span>
            </span>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="button"
                    id="moveTechnicalSelected"
                    class="btn technical-move-btn">
                <i class="fa fa-share"></i> Move Selected
            </button>

            <button type="button"
                    id="clearTechnicalSelection"
                    class="btn btn-outline-secondary">
                Clear Selection
            </button>
        </div>
    </div>

    {{-- Movement form --}}
    <form method="POST"
          action="{{ route('admin.technical-round.move-selected') }}"
          id="moveTechnicalStudentsForm">
        @csrf

        <input type="hidden"
               name="destination_round"
               id="technicalDestinationInput">

        {{-- Required only when destination is Final Students. --}}
        <input type="hidden"
               name="benefit_type"
               id="technicalBenefitTypeInput">

        <div id="technicalSelectedInputs"></div>

        {{-- Students table --}}
        <table id="technical-round-table"
               class="table table-bordered table-striped w-100">

                <thead>
                    <tr>
                        <th>
                            <input type="checkbox"
                                   id="selectAllTechnical"
                                   aria-label="Select current page">
                        </th>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>College</th>
                        <th>Source Test ID</th>
                        <!-- <th>Score</th> -->
                        <th>Status</th>
                        <th>Moved To</th>
<th>Moved On</th>
<th>Destination ID</th>
                        <th>Notes</th>
                        <th>Added On</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($technicalStudents as $technicalRound)
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="technical-student-checkbox"
                                       value="{{ $technicalRound->id }}"
                                       aria-label="Select {{ $technicalRound->student_name }}">
                            </td>

                            <td>{{ $technicalRound->id }}</td>
                            <td>{{ $technicalRound->student_name }}</td>
                            <td>{{ $technicalRound->student_email }}</td>
                            <td>{{ $technicalRound->student_mobile ?: '-' }}</td>
                            <td>{{ $technicalRound->college_name ?: '-' }}</td>
                            <td>{{ $technicalRound->source_student_test_id }}</td>
                            <td>{{ $technicalRound->score }}</td>

                            <!-- <td>
                                @php
                                    $statusClass = match (strtolower((string) $technicalRound->status)) {
                                        'selected', 'passed', 'completed' => 'bg-success',
                                        'rejected', 'failed' => 'bg-danger',
                                        'in_progress', 'in progress' => 'bg-warning text-dark',
                                        default => 'bg-secondary',
                                    };
                                @endphp

                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $technicalRound->status)) }}
                                </span>
                            </td> -->
                            <td>
    @if($technicalRound->final_record_id)
        <span class="badge bg-success">Final Students</span>
    @elseif($technicalRound->machine_round_record_id)
        <span class="badge bg-primary">Machine Round</span>
    @else
        <span class="badge bg-secondary">Not Moved</span>
    @endif
</td>

<td>
    @if($technicalRound->final_record_id)
        {{ $technicalRound->final_moved_at
            ? \Carbon\Carbon::parse($technicalRound->final_moved_at)->format('d M Y')
            : '-' }}
    @elseif($technicalRound->machine_round_record_id)
        {{ $technicalRound->machine_round_moved_at
            ? \Carbon\Carbon::parse($technicalRound->machine_round_moved_at)->format('d M Y')
            : '-' }}
    @else
        -
    @endif
</td>

<td>
    @if($technicalRound->final_record_id)
        #{{ $technicalRound->final_record_id }}
    @elseif($technicalRound->machine_round_record_id)
        #{{ $technicalRound->machine_round_record_id }}
    @else
        -
    @endif
</td>
                            <td>{{ $technicalRound->notes ?: '-' }}</td>

                            <td>
                                {{ $technicalRound->created_at
                                    ? $technicalRound->created_at->format('d M Y')
                                    : '-' }}
                            </td>
                        </tr>
                    
                    @endforeach
                </tbody>
        </table>
    </form>

</div>

{{-- Move Selected Modal --}}
<div class="modal fade"
     id="moveTechnicalModal"
     tabindex="-1"
     aria-labelledby="moveTechnicalModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="moveTechnicalModalLabel">
                    Move Selected Students
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p class="mb-3">
                    Select the destination round for
                    <strong><span id="technicalModalSelectedCount">0</span></strong>
                    selected student(s).
                </p>

                <label for="technicalDestination" class="form-label">
                    Destination Round <span class="text-danger">*</span>
                </label>

                <select id="technicalDestination" class="form-select">
                    <option value="">-- Choose Round --</option>
                    <option value="machine_round">Machine Round</option>
                    <option value="final">Final Students</option>
                </select>

                <div id="technicalBenefitTypeWrapper" class="mt-3" style="display: none;">
                    <label for="technicalBenefitType" class="form-label">
                        Internship Category <span class="text-danger">*</span>
                    </label>

                    <select id="technicalBenefitType" class="form-select">
                        <option value="">-- Choose Internship Category --</option>
                        <option value="free">Free Internship</option>
                        <option value="discount">Discounted Internship</option>
                        <option value="stipend">Stipend-Based Internship</option>
                        <option value="3_month_ppo">3-Month Internship with PPO</option>
                    </select>
                    <div class="form-text">
                        Required when moving students to Final Students.
                    </div>
                </div>

                <div class="form-text mt-2">
                    Selected students will be moved to the chosen round.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        id="confirmTechnicalMove"
                        class="btn technical-move-btn">
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

    const table = $('#technical-round-table').DataTable({
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        paging: true,
        searching: false,
        info: true,
        scrollX: true,
        // DataTables places its horizontal scrollbar immediately below the table,
        // before the bottom information and pagination controls.
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"i>>' +
             'rt' +
             '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        language: {
            info: 'Showing _START_ to _END_ of _TOTAL_ students',
            infoEmpty: 'Showing 0 to 0 students',
            lengthMenu: 'Show _MENU_ students per page',
            emptyTable: 'No Technical Round students found.',
            zeroRecords: 'No matching records found.'
        },
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ]
    });

    function updateSelectedCount() {
        $('#technicalSelectedCount').text(selectedIds.size);
        $('#technicalModalSelectedCount').text(selectedIds.size);
    }

    function syncTechnicalChecks() {
        // Only rows on the currently visible DataTables page.
        const current = $(table.rows({ page: 'current' }).nodes())
            .find('.technical-student-checkbox');

        current.each(function () {
            this.checked = selectedIds.has(String(this.value));
        });

        const checkedCount = current.filter(':checked').length;

        $('#selectAllTechnical')
            .prop('checked', current.length > 0 && checkedCount === current.length)
            .prop('indeterminate', checkedCount > 0 && checkedCount < current.length);

        updateSelectedCount();
    }

    function renderSelectedInputs() {
        const container = $('#technicalSelectedInputs');
        container.empty();

        selectedIds.forEach(function (id) {
            $('<input>', {
                type: 'hidden',
                name: 'technical_round_ids[]',
                value: id
            }).appendTo(container);
        });
    }

    function showMessage(options) {
        if (typeof Swal !== 'undefined') {
            Swal.fire(options);
        } else {
            alert((options.title ? options.title + '\n' : '') + (options.text || ''));
        }
    }

    // Individual selection.
    $('#technical-round-table').on(
        'change',
        '.technical-student-checkbox',
        function () {
            const id = String(this.value);

            if (this.checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }

            syncTechnicalChecks();
        }
    );

    // Select all records on the currently displayed page.
    $('#selectAllTechnical').on('change', function () {
        const checked = this.checked;

        // Select/deselect only the current DataTables page. Selections on
        // other pages remain in selectedIds.
        $(table.rows({ page: 'current' }).nodes())
            .find('.technical-student-checkbox').each(function () {
                const id = String(this.value);

                this.checked = checked;

                if (checked) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }
            });

        syncTechnicalChecks();
    });

    table.on('draw', function () {
        syncTechnicalChecks();
    });
    syncTechnicalChecks();

    // Clear selection.
    $('#clearTechnicalSelection').on('click', function () {
        selectedIds.clear();
        syncTechnicalChecks();
    });

    // Open the destination popup.
    $('#moveTechnicalSelected').on('click', function () {
        if (selectedIds.size === 0) {
            showMessage({
                icon: 'warning',
                title: 'No Students Selected',
                text: 'Please select at least one student.',
                confirmButtonText: 'OK',
                reverseButtons: false
            });

            return;
        }

        $('#technicalDestination').val('');
        $('#technicalBenefitType').val('');
        $('#technicalBenefitTypeInput').val('');
        $('#technicalBenefitTypeWrapper').hide();
        $('#technicalModalSelectedCount').text(selectedIds.size);

        const modalElement = document.getElementById('moveTechnicalModal');

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        } else if ($.fn.modal) {
            $('#moveTechnicalModal').modal('show');
        } else {
            showMessage({
                icon: 'error',
                title: 'Popup Unavailable',
                text: 'Bootstrap modal JavaScript is not loaded.',
                confirmButtonText: 'OK',
                reverseButtons: false
            });
        }
    });

    // Ask for internship category only when Final Students is selected.
    $('#technicalDestination').on('change', function () {
        const isFinal = $(this).val() === 'final';
        $('#technicalBenefitTypeWrapper').toggle(isFinal);
        if (!isFinal) {
            $('#technicalBenefitType').val('');
            $('#technicalBenefitTypeInput').val('');
        }
    });

    // Confirm destination and submit using the Technical Round route.
    $('#confirmTechnicalMove').on('click', function () {
        const destination = $('#technicalDestination').val();
        const benefitType = $('#technicalBenefitType').val();

        if (!destination) {
            showMessage({
                icon: 'warning',
                title: 'Choose Round',
                text: 'Please select a destination round.',
                confirmButtonText: 'OK',
                reverseButtons: false
            });

            return;
        }

        if (destination === 'final' && !benefitType) {
            showMessage({
                icon: 'warning',
                title: 'Choose Internship Category',
                text: 'Please select an internship category before moving students to Final Students.',
                confirmButtonText: 'OK',
                reverseButtons: false
            });
            return;
        }

        const destinationName = destination === 'final'
            ? 'Final Students'
            : 'Machine Round';

        const count = selectedIds.size;

        const confirmMove = function () {
            $('#technicalDestinationInput').val(destination);
            $('#technicalBenefitTypeInput').val(destination === 'final' ? benefitType : '');
            renderSelectedInputs();

            // Prevent accidental double submission.
            $('#confirmTechnicalMove').prop('disabled', true);

            $('#moveTechnicalStudentsForm')[0].submit();
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'question',
                title: 'Confirm Movement',
                text: 'Move ' + count + ' selected student(s) to ' + destinationName + '?',
                showCancelButton: true,
                confirmButtonText: 'Yes, Move',
                cancelButtonText: 'Cancel',
                reverseButtons: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    confirmMove();
                }
            });
        } else if (confirm(
            'Move ' + count + ' selected student(s) to ' + destinationName + '?'
        )) {
            confirmMove();
        }
    });

    // Search-as-you-type with debounce.
    let searchTimer;

    $('.technical-text-filter').on('input', function () {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(function () {
            $('#technicalFilterForm').trigger('submit');
        }, 400);
    });

    // Dropdown filters submit immediately.
    $('.technical-select-filter').on('change', function () {
        $('#technicalFilterForm').trigger('submit');
    });
});
</script>
@endpush
