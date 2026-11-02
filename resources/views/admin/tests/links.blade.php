@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-3">
        <div class="col-md-7">
            <h1 class="page_heading mb-1">Student Links: {{ $test->title }}</h1>
            <p class="text-muted mb-0">Set a separate start time, deadline, and enable/disable status for each college.</p>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.tests.links.export', $test->id) }}" class="btn btn-success me-2">
                <i class="fa fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('admin.tests.index') }}" class="btn btn-secondary">Back to Tests</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-info">
        <strong>Important:</strong> If <code>Valid Until</code> is empty, students cannot start the test. Their enquiry page should display
        <em>“Test has not been started by the admin yet.”</em> until a deadline is saved. The student-side enforcement will be added in the Frontend section.
    </div>

    {{-- Added bulk actions; these routes must be registered in web.php. --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Bulk College Link Actions</h5>
            <form id="bulkStatusForm" action="{{ route('admin.tests.links.bulk-status', $test->id) }}" method="POST">
                @csrf
                <input type="hidden" name="scope" id="bulkScope" value="selected">
                <input type="hidden" name="status" id="bulkStatus" value="enable">
                <div id="bulkStatusIds"></div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success" onclick="submitBulkStatus('selected', 'enable')">Enable Selected</button>
                    <button type="button" class="btn btn-warning" onclick="submitBulkStatus('selected', 'disable')">Disable Selected</button>
                    <button type="button" class="btn btn-outline-success" onclick="submitBulkStatus('all', 'enable')">Enable All Colleges</button>
                    <button type="button" class="btn btn-outline-danger" onclick="submitBulkStatus('all', 'disable')">Disable All Colleges</button>
                </div>
            </form>
            <hr>
            <form id="bulkTimingForm" action="{{ route('admin.tests.links.bulk-settings', $test->id) }}" method="POST">
                @csrf
                <div id="bulkTimingIds"></div>
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Valid From</label>
                        <input type="datetime-local" name="valid_from" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valid Until</label>
                        <input type="datetime-local" name="valid_until" class="form-control">
                    </div>
                    <div class="col-md-auto">
                        <button type="button" class="btn btn-primary" onclick="submitBulkTiming()">Update Timing for Selected</button>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">Select one or more college links first. A blank date clears that date for selected links.</small>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle" id="linksTable" style="width:100%">
            <thead>
                <tr>
                    <th><input type="checkbox" class="select-all-links-checkbox" title="Select all filtered college links" style="position:relative; z-index:5; cursor:pointer;"></th>
                    <th>#</th>
                    <th>College</th>
                    <th>Students</th>
                    <th>Last Activity</th>
                    <th style="min-width:280px">Availability Settings</th>
                    <th>Status</th>
                    <th style="min-width:280px">Student Link</th>
                    <th style="min-width:250px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($links as $link)
                    @php
                        $testUrl = route('student.test.slug', $link->slug);
                        $count = $collegeStats[$link->college_id]->total_students ?? 0;
                        $now = now();

                        // Parse database values explicitly; this works whether the model returns strings or Carbon instances.
                        $validFrom = $link->valid_from
                            ? \Illuminate\Support\Carbon::parse($link->valid_from)
                            : null;

                        $validUntil = $link->valid_until
                            ? \Illuminate\Support\Carbon::parse($link->valid_until)
                            : null;

                        if (!$link->is_enabled) {
                            $availabilityStatus = 'Disabled';
                            $statusClass = 'bg-secondary';
                        } elseif (!$validUntil) {
                            $availabilityStatus = 'Not Started';
                            $statusClass = 'bg-warning text-dark';
                        } elseif ($validUntil->lte($now)) {
                            $availabilityStatus = 'Expired';
                            $statusClass = 'bg-danger';
                        } elseif ($validFrom && $validFrom->gt($now)) {
                            $availabilityStatus = 'Scheduled';
                            $statusClass = 'bg-info text-dark';
                        } else {
                            $availabilityStatus = 'Enabled';
                            $statusClass = 'bg-success';
                        }
                    @endphp
                    <tr>
                        <td><input type="checkbox" class="college-link-checkbox" value="{{ $link->id }}" aria-label="Select {{ $link->college->full_name ?? 'college' }}"></td>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $link->college->full_name ?? '-' }}</td>
                        <td>{{ $count }}</td>
                        <td>
                            @if(isset($collegeStats[$link->college_id]) && !empty($collegeStats[$link->college_id]->last_attempt))
                                {{ \Illuminate\Support\Carbon::parse($collegeStats[$link->college_id]->last_attempt)->format('d M Y, h:i A') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.tests.links.settings', $link->id) }}" method="POST" class="availability-form">
                                @csrf
                                @method('PUT')
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Valid From (optional)</label>
                                    <input type="datetime-local" name="valid_from" class="form-control form-control-sm"
                                           value="{{ $validFrom ? $validFrom->format('Y-m-d\TH:i') : '' }}">
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label small mb-1">
                                        Valid Until <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="datetime-local"
                                        name="valid_until"
                                        class="form-control form-control-sm"
                                        value="{{ $validUntil ? $validUntil->format('Y-m-d\TH:i') : '' }}"
                                        min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}"
                                        required
                                    >

                                    <small class="text-muted">
                                        Required. Must be a future date and time.
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-sm btn-primary">Save Time Settings</button>
                            </form>
                        </td>
                        <td>
                            <span class="badge {{ $statusClass }}">{{ $availabilityStatus }}</span>
                            @if($validFrom)
                                <div class="small text-muted mt-1">From: {{ $validFrom->format('d M Y, h:i A') }}</div>
                            @endif
                            @if($validUntil)
                                <div class="small text-muted">Until: {{ $validUntil->format('d M Y, h:i A') }}</div>
                            @else
                                <div class="small text-muted mt-1">No deadline set</div>
                            @endif
                        </td>
                        <td>
                            <input type="text"
                                   class="form-control form-control-sm mb-2 test-link-input"
                                   value="{{ $testUrl }}"
                                   readonly>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ $testUrl }}" target="_blank" rel="noopener noreferrer"
                                   class="btn btn-sm btn-outline-primary">Open</a>

                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="copyTestLink(@js($testUrl))">Copy</button>

                                <button type="button" class="btn btn-sm btn-outline-dark show-test-qr-btn"
                                        data-url="{{ $testUrl }}"
                                        data-college="{{ $link->college->full_name ?? 'College' }}">
                                    QR Code
                                </button>

                                <button type="button" class="btn btn-sm btn-success whatsapp-test-link-btn"
                                        data-url="{{ $testUrl }}"
                                        data-college="{{ $link->college->full_name ?? 'College' }}">
                                    WhatsApp
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ $testUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">Open</a>

                                <form action="{{ route('admin.tests.links.toggle', $link->id) }}" method="POST" class="toggle-form d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $link->is_enabled ? 'btn-warning' : 'btn-success' }}">
                                        {{ $link->is_enabled ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.tests.links.regenerate', $link->id) }}" method="POST" class="regenerate-form d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Regenerate</button>
                                </form>

                                @if($count == 0)
                                    <form action="{{ route('admin.tests.links.destroy', $link->id) }}" method="POST" class="delete-form d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger btn-delete">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

    {{-- Test link QR Code modal --}}
    <div class="modal fade" id="testQrCodeModal" tabindex="-1" aria-labelledby="testQrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testQrCodeModalLabel">Test Link QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p id="testQrCollegeName" class="fw-bold mb-3"></p>
                    <div id="testLinkQrCode" class="d-flex justify-content-center mb-3"></div>
                    <a class="small text-muted d-block text-break mt-3" id="testQrLinkText" href="#" target="_blank" rel="noopener noreferrer"></a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="shareTestQrImageBtn">Share QR on WhatsApp</button>
                    <button type="button" class="btn btn-primary" id="downloadTestQrBtn">Download QR</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
let selectedCollegeLinks = new Set();

$(document).ready(function () {
    // Existing DataTable setup retained; only one DataTable instance is created.
    const table = $('#linksTable').DataTable({
        ordering: false,       // Disable sorting on every column
        pageLength: 50,
        lengthMenu: [10, 25, 50, 100],
        order: [],
        scrollX: true,
        language: {
            emptyTable: `No links available.<br><br><a href="{{ route('admin.tests.edit', $test->id) }}" class="btn btn-sm btn-primary">Add College for this Test</a>`
        }
    });

    // Keep selected IDs when changing DataTables page or search.
    $('#linksTable').on('change', '.college-link-checkbox', function () {
        if (this.checked) selectedCollegeLinks.add(String(this.value));
        else selectedCollegeLinks.delete(String(this.value));
        syncCollegeLinkCheckboxes();
    });

    // Handle both the original and DataTables scrollX cloned header checkboxes.
    $(document).off('.bulkSelectAllLinks');
    $(document).on('click.bulkSelectAllLinks', '.select-all-links-checkbox', function (event) {
        event.stopPropagation();
    });
    $(document).on('change.bulkSelectAllLinks', '.select-all-links-checkbox', function () {
        const checked = this.checked;
        table.rows({ search: 'applied' }).nodes().to$()
            .find('.college-link-checkbox').each(function () {
                if (checked) selectedCollegeLinks.add(String(this.value));
                else selectedCollegeLinks.delete(String(this.value));
            });
        syncCollegeLinkCheckboxes();
    });

    table.on('draw', syncCollegeLinkCheckboxes);

    // Open WhatsApp with the college name and test URL on a separate line.
    $('#linksTable').on('click', '.whatsapp-test-link-btn', function () {
        const url = String($(this).data('url') || '');
        const college = String($(this).data('college') || 'College');
        const message = 'Test link for ' + college + ':\n\n' + url;
        window.open('https://wa.me/?text=' + encodeURIComponent(message), '_blank');
    });

    let activeTestQrUrl = '';
    let activeTestQrCollege = '';

    // Generate QR code for this college's test link.
    $('#linksTable').on('click', '.show-test-qr-btn', function () {
        activeTestQrUrl = String($(this).data('url') || '');
        activeTestQrCollege = String($(this).data('college') || 'College');

        $('#testQrCollegeName').text(activeTestQrCollege);
        $('#testQrLinkText').text(activeTestQrUrl).attr('href', activeTestQrUrl);
        $('#testLinkQrCode').empty();

        if (typeof QRCode === 'undefined') {
            Swal.fire('QR unavailable', 'The QR code library could not load. Please check your internet connection and try again.', 'error');
            return;
        }

        new QRCode(document.getElementById('testLinkQrCode'), {
            text: activeTestQrUrl,
            width: 240,
            height: 240,
            correctLevel: QRCode.CorrectLevel.H
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('testQrCodeModal')).show();
    });

    // Download QR code as PNG.
    $('#downloadTestQrBtn').on('click', function () {
        const container = document.getElementById('testLinkQrCode');
        const canvas = container.querySelector('canvas');
        const image = container.querySelector('img');
        let dataUrl = '';

        if (canvas) dataUrl = canvas.toDataURL('image/png');
        else if (image && image.src) dataUrl = image.src;

        if (!dataUrl) {
            Swal.fire('QR not ready', 'Please generate the QR code first.', 'warning');
            return;
        }

        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = 'test-qr-' + (activeTestQrCollege.replace(/[^a-z0-9-_]/gi, '-') || 'College') + '.png';
        document.body.appendChild(link);
        link.click();
        link.remove();
    });

    // Share the actual QR image using the device/browser share sheet.
    $('#shareTestQrImageBtn').on('click', async function () {
        const container = document.getElementById('testLinkQrCode');
        const canvas = container.querySelector('canvas');
        const image = container.querySelector('img');

        if (!canvas && !image) {
            Swal.fire('QR not ready', 'Please generate the QR code first.', 'warning');
            return;
        }

        try {
            let blob;
            if (canvas) {
                blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
            } else {
                const response = await fetch(image.src);
                blob = await response.blob();
            }

            if (!blob) throw new Error('Unable to prepare QR image.');

            const safeName = activeTestQrCollege.replace(/[^a-z0-9-_]/gi, '-') || 'College';
            const qrFile = new File([blob], 'test-qr-' + safeName + '.png', { type: 'image/png' });
            const shareData = {
                title: 'Test QR Code - ' + activeTestQrCollege,
                text: 'Test link QR code for ' + activeTestQrCollege + '.',
                files: [qrFile]
            };

            if (navigator.share && navigator.canShare && navigator.canShare({ files: [qrFile] })) {
                await navigator.share(shareData);
            } else {
                const downloadUrl = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = qrFile.name;
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(downloadUrl);
                Swal.fire('QR downloaded', 'This browser cannot share image files directly. Attach the downloaded QR image in WhatsApp.', 'info');
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                Swal.fire('Share failed', 'Could not share the QR image. Please use Download QR and attach the image in WhatsApp.', 'error');
            }
        }
    });
});

function syncCollegeLinkCheckboxes() {
    document.querySelectorAll('#linksTable .college-link-checkbox').forEach(function (checkbox) {
        checkbox.checked = selectedCollegeLinks.has(String(checkbox.value));
    });
    const selectAllCheckboxes = document.querySelectorAll('.select-all-links-checkbox');
    const visibleCheckboxes = Array.from(document.querySelectorAll('#linksTable tbody .college-link-checkbox'));
    const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);
    const someChecked = visibleCheckboxes.some(cb => cb.checked);
    selectAllCheckboxes.forEach(function (selectAll) {
        selectAll.checked = allChecked;
        selectAll.indeterminate = someChecked && !allChecked;
    });
}

function appendSelectedIds(containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    selectedCollegeLinks.forEach(function (id) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'link_ids[]';
        input.value = id;
        container.appendChild(input);
    });
}

function submitBulkStatus(scope, status) {
    if (scope === 'selected' && selectedCollegeLinks.size === 0) {
        Swal.fire('No links selected', 'Please select at least one college link.', 'warning');
        return;
    }
    const message = scope === 'all'
        ? `This will ${status} every college link for this opened test.`
        : `This will ${status} the selected college links.`;
    Swal.fire({
        title: 'Confirm bulk action', text: message, icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Yes, continue', confirmButtonColor: status === 'enable' ? '#198754' : '#dc3545',
        cancelButtonColor: '#6c757d'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        document.getElementById('bulkScope').value = scope;
        document.getElementById('bulkStatus').value = status;
        appendSelectedIds('bulkStatusIds');
        document.getElementById('bulkStatusForm').submit();
    });
}

function submitBulkTiming() {
    if (selectedCollegeLinks.size === 0) {
        Swal.fire('No links selected', 'Please select the college links to update.', 'warning');
        return;
    }
    const form = document.getElementById('bulkTimingForm');
    const from = form.querySelector('[name="valid_from"]').value;
    const until = form.querySelector('[name="valid_until"]').value;
    if (from && until && new Date(until) < new Date(from)) {
        Swal.fire('Invalid timing', 'Valid Until must be after or equal to Valid From.', 'warning');
        return;
    }
    Swal.fire({
        title: 'Update timing?',
        text: `Update timing for ${selectedCollegeLinks.size} selected college link(s)? Blank dates will be cleared.`,
        icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, update', cancelButtonColor: '#6c757d'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        appendSelectedIds('bulkTimingIds');
        form.submit();
    });
}

function copyTestLink(url) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({ icon: 'success', title: 'Copied!', text: 'Test link copied to clipboard!', confirmButtonText: 'OK' });
        }).catch(() => fallbackCopyTestLink(url));
    } else {
        fallbackCopyTestLink(url);
    }
}

function fallbackCopyTestLink(url) {
    const input = document.createElement('textarea');
    input.value = url;
    input.style.position = 'fixed';
    input.style.left = '-9999px';
    document.body.appendChild(input);
    input.select();
    const copied = document.execCommand('copy');
    input.remove();

    Swal.fire({
        icon: copied ? 'success' : 'error',
        title: copied ? 'Copied!' : 'Failed',
        text: copied ? 'Test link copied to clipboard!' : 'Failed to copy link. Please copy it from the input field.',
        confirmButtonText: 'OK'
    });
}

document.querySelectorAll('.regenerate-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const currentForm = this;
        Swal.fire({
            title: 'Regenerate Test Link?',
            text: 'The old URL will stop working. Availability settings will be preserved.',
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, regenerate', reverseButtons: false
        }).then(result => { if (result.isConfirmed) currentForm.submit(); });
    });
});

document.querySelectorAll('.toggle-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const currentForm = this;
        const buttonText = this.querySelector('button').textContent.trim();
        const disabling = buttonText === 'Disable';
        Swal.fire({
            title: disabling ? 'Disable this test link?' : 'Enable this test link?',
            text: disabling ? 'Students will be blocked from using this link immediately.' : 'The link will be enabled only if its time settings allow access.',
            icon: 'warning', showCancelButton: true, confirmButtonColor: disabling ? '#d33' : '#198754',
            cancelButtonColor: '#6c757d', confirmButtonText: disabling ? 'Yes, disable' : 'Yes, enable', reverseButtons: false
        }).then(result => { if (result.isConfirmed) currentForm.submit(); });
    });
});

document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function () {
        const form = this.closest('form');
        Swal.fire({
            title: 'Delete Link?', text: 'This action cannot be undone.', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it', reverseButtons: false
        }).then(result => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
