@extends('layouts.app')

@section('content')
<div class="container">

<h4>Edit HOD / TPO</h4>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('hods.update', $hod->id) }}">
@csrf
@method('PUT')

<div class="row">

<!-- College -->
<div class="col-md-6 mb-3">
    <label>College</label>
    <select name="college_id" class="form-control @error('college_id') is-invalid @enderror">
        @foreach($colleges as $college)
            <option value="{{ $college->id }}"
                {{ old('college_id',$hod->college_id)==$college->id?'selected':'' }}>
                {{ $college->college_name }}
            </option>
        @endforeach
    </select>
    @error('college_id') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<!-- ================= HOD ================= -->
<h5 class="col-12 mt-3">HOD Details</h5>

<div class="col-md-4 mb-3">
    <label>HOD Name</label>
    <input type="text" name="hod_name" value="{{ old('hod_name',$hod->hod_name) }}"
           class="form-control @error('hod_name') is-invalid @enderror">
    @error('hod_name') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="col-md-4 mb-3">
    <label>HOD Gender</label>
    <select name="hod_gender" class="form-control @error('hod_gender') is-invalid @enderror">
        <option value="">Select Gender</option>
        <option value="Male" {{ old('hod_gender',$hod->hod_gender)=='Male'?'selected':'' }}>Male</option>
        <option value="Female" {{ old('hod_gender',$hod->hod_gender)=='Female'?'selected':'' }}>Female</option>
        <option value="Other" {{ old('hod_gender',$hod->hod_gender)=='Other'?'selected':'' }}>Other</option>
    </select>
    @error('hod_gender') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="col-md-4 mb-3">
    <label>HOD Contact</label>
    <input type="text" name="hod_contact" value="{{ old('hod_contact',$hod->hod_contact) }}"
           class="form-control @error('hod_contact') is-invalid @enderror" maxlength="10">
    @error('hod_contact') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<!-- HOD Emails -->
<div class="col-md-12 mb-3">
<label>HOD Emails (Select Primary)</label>

@error('hod_emails') <small class="text-danger d-block">{{ $message }}</small> @enderror

<div id="hod-emails">

@foreach($hod->hodEmails as $i => $mail)
<div class="d-flex align-items-center mb-2 email-row">
    <input type="email"
           name="hod_emails[]"
           value="{{ old('hod_emails.'.$i, $mail->email) }}"
           class="form-control me-2 @error('hod_emails.'.$i) is-invalid @enderror">

    <input type="radio"
           name="hod_primary"
           value="{{ $i }}"
           {{ old('hod_primary', $mail->is_primary ? $i : null) == $i ? 'checked' : '' }}>

    <small class="ms-1">Primary</small>

    <button type="button"
            class="btn btn-sm btn-danger ms-2"
            onclick="removeRow(this)">❌</button>
</div>

@error('hod_emails.'.$i)
    <small class="text-danger d-block">{{ $message }}</small>
@enderror

@endforeach

@if($hod->hodEmails->count() == 0)
    <div class="d-flex align-items-center mb-2 email-row">
        <input type="email" name="hod_emails[]" class="form-control me-2">
        <input type="radio" name="hod_primary" value="0" checked>
        <small class="ms-1">Primary</small>
    </div>
@endif

</div>

@error('hod_primary')
    <small class="text-danger d-block">{{ $message }}</small>
@enderror

<button type="button" class="btn btn-sm btn-secondary" onclick="addHodEmail()">+ Add More</button>
</div>


<!-- ================= TPO ================= -->
<h5 class="col-12 mt-4">TPO Details</h5>

<div class="col-md-4 mb-3">
    <label>TPO Name</label>
    <input type="text" name="tpo_name" value="{{ old('tpo_name',$hod->tpo_name) }}"
           class="form-control @error('tpo_name') is-invalid @enderror">
    @error('tpo_name') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="col-md-4 mb-3">
    <label>TPO Gender</label>
    <select name="tpo_gender" class="form-control @error('tpo_gender') is-invalid @enderror">
        <option value="">Select Gender</option>
        <option value="Male" {{ old('tpo_gender',$hod->tpo_gender)=='Male'?'selected':'' }}>Male</option>
        <option value="Female" {{ old('tpo_gender',$hod->tpo_gender)=='Female'?'selected':'' }}>Female</option>
        <option value="Other" {{ old('tpo_gender',$hod->tpo_gender)=='Other'?'selected':'' }}>Other</option>
    </select>
    @error('tpo_gender') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="col-md-4 mb-3">
    <label>TPO Contact</label>
    <input type="text" name="tpo_contact" value="{{ old('tpo_contact',$hod->tpo_contact) }}"
           class="form-control @error('tpo_contact') is-invalid @enderror" maxlength="10">
    @error('tpo_contact') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<!-- TPO Emails -->
<div class="col-md-12 mb-3">
<label>TPO Emails (Select Primary)</label>

@error('tpo_emails') <small class="text-danger d-block">{{ $message }}</small> @enderror

<div id="tpo-emails">
@foreach($hod->tpoEmails as $i => $mail)
<div class="d-flex align-items-center mb-2 email-row">
    <input type="email" name="tpo_emails[]" value="{{ old('tpo_emails.'.$i,$mail->email) }}"
           class="form-control me-2 @error('tpo_emails.'.$i) is-invalid @enderror">

    <input type="radio" name="tpo_primary" value="{{ $i }}"
           {{ old('tpo_primary',$mail->is_primary? $i:null)==$i?'checked':'' }}>
    <small class="ms-1">Primary</small>

    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeRow(this)">❌</button>
</div>
@error('tpo_emails.'.$i) <small class="text-danger d-block">{{ $message }}</small> @enderror
@endforeach
</div>

@error('tpo_primary') <small class="text-danger d-block">{{ $message }}</small> @enderror
<button type="button" class="btn btn-sm btn-secondary" onclick="addTpoEmail()">+ Add More</button>
</div>

</div>

<button class="btn btn-primary mt-3">Update</button>
<a href="{{ route('hods.index') }}" class="btn btn-secondary mt-3">Back</a>

</form>
</div>

{{-- SAME JS --}}
<script>
function addHodEmail() {
    let index = document.querySelectorAll('#hod-emails .email-row').length;
    document.getElementById('hod-emails').insertAdjacentHTML(
        'beforeend',
        `<div class="d-flex align-items-center mb-2 email-row">
            <input type="email" name="hod_emails[]" class="form-control me-2">
            <input type="radio" name="hod_primary" value="${index}">
            <small class="ms-1">Primary</small>
            <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeRow(this)">❌</button>
        </div>`
    );
}

function addTpoEmail() {
    let index = document.querySelectorAll('#tpo-emails .email-row').length;
    document.getElementById('tpo-emails').insertAdjacentHTML(
        'beforeend',
        `<div class="d-flex align-items-center mb-2 email-row">
            <input type="email" name="tpo_emails[]" class="form-control me-2">
            <input type="radio" name="tpo_primary" value="${index}">
            <small class="ms-1">Primary</small>
            <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeRow(this)">❌</button>
        </div>`
    );
}

function removeRow(btn) {
    let row = btn.parentElement;
    let container = row.parentElement;
    let wasPrimary = row.querySelector('input[type=radio]').checked;

    row.remove();

    let radios = container.querySelectorAll('input[type=radio]');
    radios.forEach((radio, i) => radio.value = i);

    if (wasPrimary && radios.length > 0) radios[0].checked = true;
}
</script>
@endsection
