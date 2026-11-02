@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }

    #cardsTable {
        width: 100% !important;
    }
</style>

<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Visiting Cards</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2 flex-wrap">

                <button type="button"
                        id="bulkDeleteBtn"
                        class="btn"
                        style="background:#dc3545;color:#fff;">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>

                <a href="{{ route('visiting-cards.create') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Add Visiting Card
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Bulk-delete configuration --}}
    <input type="hidden"
           id="bulkDeleteUrl"
           value="{{ route('visiting-cards.bulkDelete') }}">

    <input type="hidden"
           id="bulkDeleteToken"
           value="{{ csrf_token() }}">

    <div class="table-responsive">
        <table id="cardsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center">
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th>#</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Company</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($cards as $card)
                <tr>
                    <td class="text-center">
                        <input type="checkbox"
                               class="row-checkbox"
                               value="{{ $card->id }}">
                    </td>

                    <td></td>

                    <td>{{ $card->name }}</td>

                    <td>{{ $card->designation }}</td>

                    <td>{{ $card->company_name }}</td>

                    <td>{{ $card->phone_primary }}</td>

                    <td>
                        <a href="{{ route('visiting-cards.show', $card) }}"
                           class="btn btn-sm"
                           title="View">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('visiting-cards.edit', $card) }}"
                           class="btn btn-sm"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>


                            <form action="{{ route('visiting-cards.destroy', $card) }}"
                                  method="POST"
                                  class="d-inline visiting-card-delete-form">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    var table = $('#cardsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        autoWidth: true,
        scrollX: true,
        columnDefs: [
            {
                targets: [0, 1],
                searchable: false,
                orderable: false
            },
            {
                targets: 6,
                searchable: false,
                orderable: false
            }
        ]
    });

    // Preserve selected card IDs across pagination, searching and redraws.
    var selectedVisitingCardIds = new Set();

    function updateCardCheckboxes() {
        $('#cardsTable tbody .row-checkbox').each(function () {
            this.checked = selectedVisitingCardIds.has(String(this.value));
        });

        var checkboxes = $('#cardsTable tbody .row-checkbox');
        var checkedCount = checkboxes.filter(':checked').length;

        $('input[type="checkbox"][id="checkAll"]').prop(
            'checked',
            checkboxes.length > 0 && checkedCount === checkboxes.length
        );
    }

    // Individual selection.
    $(document).on('change', '#cardsTable tbody .row-checkbox', function () {
        var id = String(this.value);

        if (this.checked) {
            selectedVisitingCardIds.add(id);
        } else {
            selectedVisitingCardIds.delete(id);
        }

        updateCardCheckboxes();
    });

    // Select/deselect only the current DataTable page.
    $(document).on('change', 'input[type="checkbox"][id="checkAll"]', function () {
        var checked = this.checked;

        $('#cardsTable tbody .row-checkbox').each(function () {
            var id = String(this.value);

            if (checked) {
                selectedVisitingCardIds.add(id);
            } else {
                selectedVisitingCardIds.delete(id);
            }
        });

        updateCardCheckboxes();
    });

    table.on('draw.dt', function () {
        var pageInfo = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = pageInfo.start + i + 1;
        });

        updateCardCheckboxes();
    });

    table.draw();

    // Bulk delete selected visiting cards.
    $('#bulkDeleteBtn').on('click', function () {

        if (selectedVisitingCardIds.size === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Record Selected',
                text: 'Please select at least 1 record.'
            });
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Delete Selected Visiting Cards?',
            text: 'Are you sure you want to delete the selected records?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: $('#bulkDeleteUrl').val(),
                type: 'POST',
                data: {
                    _token: $('#bulkDeleteToken').val(),
                    ids: Array.from(selectedVisitingCardIds).join(',')
                },
                success: function (response) {

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted Successfully',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Unable to delete selected records.'
                        });
                    }
                },
                error: function (xhr) {
                    var message = 'Unable to delete selected records.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                }
            });
        });
    });

});


$(document).on('submit', '.visiting-card-delete-form', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    var form = this;

    Swal.fire({
        icon: 'warning',
        title: 'Delete Visiting Card?',
        text: 'Are you sure you want to delete this visiting card?',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: false
    }).then(function (result) {
        if (result.isConfirmed) {
            HTMLFormElement.prototype.submit.call(form);
        }
    });

    return false;
});

</script>
@endpush