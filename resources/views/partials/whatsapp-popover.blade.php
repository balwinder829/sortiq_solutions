<div id="whatsappPopoverContent" style="display:none;min-width:500px;">

    @if(!session('whatsapp_token'))
        <div class="alert alert-warning">
            WhatsApp is not logged in.
        </div>
    @endif

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="append_name" id="append_name">
        <label class="form-check-label" for="append_name">
            Append Name
        </label>
    </div>

    <div class="mb-3">
        <label class="form-label">Custom Message</label>
        <textarea
            class="form-control"
            id="customMessage"
            name="customMessage"
            rows="5"></textarea>
    </div>

    <hr>

    <div class="mb-3">
        <label class="form-label">Upload New File</label>
        <input
            type="file"
            class="form-control"
            id="whatsappFile"
            name="whatsappFile"
            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
    </div>
    @if (isset($previousWhatsappUploadedFiles))
        <div class="mb-3">
            <label class="form-label">Or Select Existing File</label>

            <select
                class="form-select"
                id="existingFile"
                name="existingFile">

                <option value="">Select File</option>
                @foreach($previousWhatsappUploadedFiles as $file)
                    <option value="{{ $file['path'] }}">
                        {{ $file['name'] }}
                    </option>
                @endforeach

            </select>
        </div>
    @endif
    <div id="selectedFilePreview" class="small text-muted mb-3"></div>

    <button
        type="button"
        id="sendWhatsappNotification"
        class="btn btn-warning">
        Send
    </button>

    <div id="message_loader" style="display:none;">
        <i class="fa fa-spinner fa-spin"></i> Sending...
    </div>

</div>

<button type="button" id="whatsappBtn" class="btn btn-primary {{ isset($add_margin) ? $add_margin : "" }}">
    Send Whatsapp Notification
</button>

<script>
    
    $(document).on('change', '#existingFile', function () {
        let fileName = $(this).find('option:selected').text();
        $('#selectedFilePreview').html(
            fileName !== 'Select File'
                ? 'Selected: ' + fileName
                : ''
        );
    });
</script>
<style>
    .popover.whatsapp-popover {
        min-width: 400px;
        max-width: 500px;
    }
</style>