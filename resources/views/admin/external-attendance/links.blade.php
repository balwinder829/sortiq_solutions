
@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <h1 class="page_heading">Form Links : {{ $test->title }}</h1>
        </div>
        
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.external-attendance.links.export', $test->id) }}"
                   class="btn btn-success me-2">
                    <i class="fa fa-file-excel"></i> Export Excel
                </a>

                <a href="{{ route('admin.external-attendance.index') }}"
                   class="btn btn-secondary">
                    Back
                </a>
            </div>

    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- EXISTING BULK ACTIONS: KEEP FUNCTIONALITY --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Bulk Link Actions</h5>

            <form id="bulkForm"
                  method="POST"
                  action="{{ route('admin.external-attendance.links.bulk', $test->id) }}">
                @csrf

                <div class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label for="bulkScope" class="form-label fw-bold">
                            Apply To
                        </label>
                        <select name="scope" id="bulkScope" class="form-select">
                            <option value="selected">Selected Colleges</option>
                            <option value="all">All Colleges</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="bulkAction" class="form-label fw-bold">
                            Action
                        </label>
                        <select name="action" id="bulkAction"
                                class="form-select" required>
                            <option value="enable">Enable Links</option>
                            <option value="disable">Disable Links</option>
                            <option value="start">Change Start Date/Time</option>
                            <option value="end">Change End Date/Time</option>
                            <option value="both">Change Both Dates</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-none" id="startDateBox">
                        <label for="validFrom" class="form-label fw-bold">
                            Start Date/Time
                        </label>
                        <input type="datetime-local"
                               name="valid_from"
                               id="validFrom"
                               class="form-control">
                    </div>

                    <div class="col-md-3 d-none" id="endDateBox">
                        <label for="validUntil" class="form-label fw-bold">
                            End Date/Time
                        </label>
                        <input type="datetime-local"
                               name="valid_until"
                               id="validUntil"
                               class="form-control">
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            Bulk Actions
                        </button>
                    </div>
                </div>

                <small class="text-muted d-block mt-2">
                    Bulk changes apply to the selected colleges or all colleges.
                    Row-wise controls below operate on one college at a time.
                </small>
            </form>
        </div>
    </div>

    {{-- COLLEGE LINKS TABLE --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle"
                       id="linksTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>#</th>
                            <th>College</th>
                            <th>Last Activity</th>
                            <th style="min-width:330px;">Availability Settings</th>
                            <th style="min-width:100px;">Status</th>
                            <th style="min-width:210px;">Student Link</th>
                            <th style="min-width:170px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($links as $link)
                        @php
                            $testUrl = route('form.view', $link->slug);
                        @endphp

                        <tr>
                            {{-- Checkbox used by existing bulk form --}}
                            <td>
                                <input type="checkbox"
                                       class="college-checkbox"
                                       name="link_ids[]"
                                       value="{{ $link->id }}"
                                       form="bulkForm">
                            </td>

                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $link->college->full_name ?? '-' }}
                            </td>

                            {{-- Last Activity --}}
                            <td>
                                @if(isset($lastAttempts[$link->college_id]))
                                    {{ \Carbon\Carbon::parse(
                                        $lastAttempts[$link->college_id]
                                    )->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            {{-- ROW-WISE TIMING --}}
                            <td>
                                <form method="POST"
                                      action="{{ route('admin.external-attendance.links.college-timing', $test->id) }}"
                                      class="college-timing-form">
                                    @csrf

                                    <input type="hidden"
                                           name="college_id"
                                           value="{{ $link->college_id }}">

                                    <label class="form-label">
                                        Valid From (optional)
                                    </label>

                                    <input type="datetime-local"
                                           name="valid_from"
                                           class="form-control mb-2"
                                           value="{{ $link->valid_from
                                               ? \Carbon\Carbon::parse($link->valid_from)->format('Y-m-d\TH:i')
                                               : '' }}">

                                    <label class="form-label">
                                        Valid Until
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="datetime-local"
                                           name="valid_until"
                                           class="form-control mb-2"
                                           required
                                           value="{{ $link->valid_until
                                               ? \Carbon\Carbon::parse($link->valid_until)->format('Y-m-d\TH:i')
                                               : '' }}">

                                    <small class="text-muted d-block mb-2">
                                        Applies to this college's attendance link
                                        and all exams mapped to this form.
                                    </small>

                                    <button type="submit"
                                            class="btn btn-sm btn-primary">
                                        Save Time Settings
                                    </button>
                                </form>
                            </td>

                            {{-- ROW-WISE ENABLE / DISABLE --}}
                            <td>
                                @if($link->is_enabled)
                                    <span class="badge bg-success mb-2">
                                        Enabled
                                    </span>
                                @else
                                    <span class="badge bg-danger mb-2">
                                        Disabled
                                    </span>
                                @endif

                                <form method="POST"
                                      action="{{ route('admin.external-attendance.links.college-status', $test->id) }}"
                                      class="college-status-form">
                                    @csrf

                                    <input type="hidden"
                                           name="college_id"
                                           value="{{ $link->college_id }}">

                                    <input type="hidden"
                                           name="is_enabled"
                                           value="{{ $link->is_enabled ? 0 : 1 }}">

                                    <button type="submit"
                                            class="btn btn-sm {{ $link->is_enabled ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $link->is_enabled ? 'Disable All' : 'Enable All' }}
                                    </button>
                                </form>
                            </td>

                            {{-- STUDENT LINK --}}
                            <td>
                                <div class="mb-3">
                                    <input type="text"
                                           class="form-control form-control-sm student-link-input"
                                           value="{{ $testUrl }}"
                                           readonly
                                           onclick="this.select()"
                                           aria-label="Attendance form link">
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ $testUrl }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="btn btn-sm btn-outline-primary">
                                        Open
                                    </a>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary copy-link-btn"
                                            data-url="{{ $testUrl }}">
                                        Copy
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-dark show-qr-btn"
                                            data-url="{{ $testUrl }}"
                                            data-college="{{ $link->college->full_name ?? 'College' }}">
                                        QR Code
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-success whatsapp-share-btn"
                                            data-url="{{ $testUrl }}"
                                            data-college="{{ $link->college->full_name ?? 'College' }}">
                                        WhatsApp
                                    </button>

                                </div>
                            </td>

                            {{-- ROW-WISE SYNC + REGENERATE --}}
                            <td>
                                <form method="POST"
                                      action="{{ route('admin.external-attendance.links.sync-college', [$test->id, $link->college_id]) }}"
                                      class="sync-college-form mb-2">
                                    @csrf

                                    <button type="submit"
                                            class="btn btn-sm btn-success w-100">
                                        Sync College Links
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('admin.external-attendance.link.regenerate', $link->id) }}"
                                      class="regenerate-form">
                                    @csrf

                                    <input type="hidden"
                                           name="regenerate_scope"
                                           value="attendance">

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger regenerate-button w-100">
                                        Regenerate Link
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No attendance links available.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- QR Code modal --}}
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p id="qrCollegeName" class="fw-bold mb-3"></p>
                    <div id="attendanceQrCode" class="d-flex justify-content-center mb-3"></div>
                    <a class="small text-muted d-block text-break mt-3" id="qrLinkText" href="#" target="_blank" rel="noopener noreferrer"></a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="shareQrImageBtn">Share QR on WhatsApp</button>
                    <button type="button" class="btn btn-primary" id="downloadQrBtn">Download QR</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function () {

    // Existing DataTable and cross-page selection
    const table = $('#linksTable').DataTable({
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        columnDefs: [
            { orderable: false, targets: [0, 7] }
        ],
        language: {
            emptyTable: 'No links available.'
        }
    });

    $('#selectAll').on('change', function () {
        $(table.rows().nodes())
            .find('.college-checkbox')
            .prop('checked', this.checked);
    });

    $('#linksTable').on('change', '.college-checkbox', function () {
        const boxes = $(table.rows().nodes()).find('.college-checkbox');

        $('#selectAll').prop(
            'checked',
            boxes.length > 0 &&
            boxes.filter(':checked').length === boxes.length
        );
    });

    // Existing bulk date field behavior
    function updateDateFields() {
        const action = $('#bulkAction').val();

        $('#startDateBox, #endDateBox').addClass('d-none');
        $('#validFrom, #validUntil').prop('required', false);

        if (action === 'start' || action === 'both') {
            $('#startDateBox').removeClass('d-none');
            $('#validFrom').prop('required', true);
        }

        if (action === 'end' || action === 'both') {
            $('#endDateBox').removeClass('d-none');
            $('#validUntil').prop('required', true);
        }
    }

    $('#bulkAction').on('change', updateDateFields);
    updateDateFields();

    // Existing bulk action confirmation
    $('#bulkForm').on('submit', function (e) {
        const form = this;
        const scope = $('#bulkScope').val();
        const action = $('#bulkAction').val();

        const selectedCount = $(table.rows().nodes())
            .find('.college-checkbox:checked').length;

        if (scope === 'selected' && selectedCount === 0) {
            e.preventDefault();

            Swal.fire(
                'Select colleges',
                'Please select at least one college.',
                'warning'
            );

            return;
        }

        e.preventDefault();

        let message = 'Apply this action to ' +
            (scope === 'all' ? 'all colleges' : 'the selected colleges') + '?';

        if (action === 'enable') {
            message += ' Each college must have a future end date.';
        }

        Swal.fire({
            title: 'Confirm Bulk Action',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, apply',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Save timing for one college and all its mapped exams
    $('#linksTable').on('submit', '.college-timing-form', function (e) {
        e.preventDefault();

        const form = this;

        Swal.fire({
            title: 'Save Time Settings?',
            text: 'The attendance link and all mapped exam links for this college will receive these times. Other colleges will not change.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Save Timing',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Enable or disable one college and its mapped exam links
    $('#linksTable').on('submit', '.college-status-form', function (e) {
        e.preventDefault();

        const form = this;
        const enabling = $(form).find('[name="is_enabled"]').val() === '1';

        Swal.fire({
            title: enabling ? 'Enable all links?' : 'Disable all links?',
            text: 'This affects the attendance link and all mapped exam links for this college only.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Sync missing exam links for one college
    $('#linksTable').on('submit', '.sync-college-form', function (e) {
        e.preventDefault();

        const form = this;

        Swal.fire({
            title: 'Sync College Links?',
            text: 'Only missing exam links for this college will be created. Existing links and settings will be preserved.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sync Links',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Existing regeneration options
    $('#linksTable').on('click', '.regenerate-button', function () {
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Regenerate Links',
            text: 'Choose whether to regenerate only the attendance link or also its mapped exam links for this college.',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Attendance Only',
            denyButtonText: 'Attendance + Exam Links',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.find('[name="regenerate_scope"]').val('attendance');
                form[0].submit();
            } else if (result.isDenied) {
                form.find('[name="regenerate_scope"]').val('both');
                form[0].submit();
            }
        });
    });

    let activeQrUrl = '';
    let activeQrCollege = '';

    // Generate QR code for the selected attendance link.
    $('#linksTable').on('click', '.show-qr-btn', function () {
        activeQrUrl = String($(this).data('url') || '');
        activeQrCollege = String($(this).data('college') || 'College');

        $('#qrCollegeName').text(activeQrCollege);
        $('#qrLinkText').text(activeQrUrl).attr('href', activeQrUrl);
        $('#attendanceQrCode').empty();

        if (typeof QRCode === 'undefined') {
            Swal.fire('QR unavailable', 'The QR code library could not load. Please check your internet connection and try again.', 'error');
            return;
        }

        new QRCode(document.getElementById('attendanceQrCode'), {
            text: activeQrUrl,
            width: 240,
            height: 240,
            correctLevel: QRCode.CorrectLevel.H
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('qrCodeModal')).show();
    });

    // Download the generated QR code as a PNG image.
    $('#downloadQrBtn').on('click', function () {
        const qrContainer = document.getElementById('attendanceQrCode');
        const canvas = qrContainer.querySelector('canvas');
        const image = qrContainer.querySelector('img');
        let dataUrl = '';

        if (canvas) {
            dataUrl = canvas.toDataURL('image/png');
        } else if (image && image.src) {
            dataUrl = image.src;
        }

        if (!dataUrl) {
            Swal.fire('QR not ready', 'Please generate the QR code first.', 'warning');
            return;
        }

        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = 'attendance-qr-' + activeQrCollege.replace(/[^a-z0-9-_]/gi, '-') + '.png';
        document.body.appendChild(link);
        link.click();
        link.remove();
    });

    // Share the actual QR image using the device/browser share sheet (e.g. WhatsApp).
    $('#shareQrImageBtn').on('click', async function () {
        const qrContainer = document.getElementById('attendanceQrCode');
        const canvas = qrContainer.querySelector('canvas');
        const image = qrContainer.querySelector('img');

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

            const safeCollegeName = activeQrCollege.replace(/[^a-z0-9-_]/gi, '-') || 'College';
            const qrFile = new File([blob], 'attendance-qr-' + safeCollegeName + '.png', { type: 'image/png' });
            const shareData = {
                title: 'QR Code - ' + activeQrCollege,
                text: 'Form QR code for ' + activeQrCollege + '.',
                files: [qrFile]
            };

            if (navigator.canShare && navigator.canShare({ files: [qrFile] }) && navigator.share) {
                await navigator.share(shareData);
            } else {
                // Image sharing is not supported in every browser. Download it for manual sharing.
                const downloadUrl = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = qrFile.name;
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(downloadUrl);
                Swal.fire('QR downloaded', 'This browser cannot share image files directly. The QR image has been downloaded; attach it in WhatsApp to share.', 'info');
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                Swal.fire('Share failed', 'Could not share the QR image. Please use Download QR and attach the image in WhatsApp.', 'error');
            }
        }
    });

    // Share the attendance link directly in WhatsApp. Keep the URL on its own
    // line so WhatsApp can recognize it as a link after the message is sent.
    $('#linksTable').on('click', '.whatsapp-share-btn', function () {
        const url = String($(this).data('url') || '').trim();
        const college = String($(this).data('college') || 'College').trim();

        if (!url) {
            Swal.fire('Link missing', 'Link is not available.', 'warning');
            return;
        }

        const message = 'Form for ' + college + ':\n\n' + url;
        const whatsappUrl = 'https://wa.me/?text=' + encodeURIComponent(message);
        window.open(whatsappUrl, '_blank');
    });

    // Copy attendance link
    $('#linksTable').on('click', '.copy-link-btn', async function () {
        const url = $(this).data('url');

        try {
            await navigator.clipboard.writeText(url);

            Swal.fire(
                'Copied!',
                'Link copied to clipboard.',
                'success'
            );
        } catch (error) {
            const input = $('<textarea>')
                .val(url)
                .css({
                    position: 'fixed',
                    left: '-9999px',
                    top: '0'
                })
                .appendTo('body');

            input[0].select();

            const copied = document.execCommand('copy');
            input.remove();

            if (copied) {
                Swal.fire('Copied!', 'Attendance link copied.', 'success');
            } else {
                Swal.fire(
                    'Copy failed',
                    'Copy the link manually from the input field.',
                    'error'
                );
            }
        }
    });

});
</script>
@endpush
