@extends('layouts.app')

@section('content')

<style>
.nav-tabs .nav-link {
    background:#f4f6f9;
    border:1px solid #dee2e6;
    margin-right:5px;
    border-radius:6px 6px 0 0;
}
.nav-tabs .nav-link:hover{
    background:#e9ecef;
}
.nav-tabs .nav-link.active{
    background:#ffffff;
    border-bottom:2px solid #0d6efd;
    font-weight:600;
}
</style>

<div class="container">

<h4>Edit HOD / TPO</h4>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Please fix the following errors:</strong>
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

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#college">
            College
        </button>
    </li>
    <li class="nav-item">
        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#hod">
            HOD Details
        </button>
    </li>
    <li class="nav-item">
        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tpo">
            TPO Details
        </button>
    </li>
    <li class="nav-item">
        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#principal">
            Principal Details
        </button>
    </li>
</ul>

<div class="tab-content border p-3">

<!-- ================= COLLEGE ================= -->
<div class="tab-pane fade show active" id="college">
<div class="row">

<div class="col-md-6 mb-3">
    <label>College</label>
    <select name="college_id" class="form-control @error('college_id') is-invalid @enderror select2" required>
        @foreach($colleges as $college)
            <option value="{{ $college->id }}"
                {{ old('college_id',$hod->college_id)==$college->id?'selected':'' }}>
                {{ $college->college_name }}
            </option>
        @endforeach
    </select>
    @error('college_id')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<!-- DESCRIPTION -->
<div class="col-md-12 mb-3">
    <label>Description</label>
    <textarea name="description"
              rows="4"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $hod->description) }}</textarea>
</div>

</div>
</div>


<!-- ================= HOD ================= -->
<div class="tab-pane fade" id="hod">
<div class="row">

<h5 class="col-12 mt-3">HOD Details</h5>

<div class="col-md-4 mb-3">
    <label>HOD Name</label>
    <input type="text" name="hod_name"
           value="{{ old('hod_name',$hod->hod_name) }}"
           class="form-control @error('hod_name') is-invalid @enderror">
</div>

<div class="col-md-4 mb-3">
    <label>HOD Gender</label>
    <select name="hod_gender" class="form-control">
        <option value="">Select Gender</option>
        <option value="Male" {{ old('hod_gender',$hod->hod_gender)=='Male'?'selected':'' }}>Male</option>
        <option value="Female" {{ old('hod_gender',$hod->hod_gender)=='Female'?'selected':'' }}>Female</option>
        <option value="Other" {{ old('hod_gender',$hod->hod_gender)=='Other'?'selected':'' }}>Other</option>
    </select>
</div>

<div class="col-md-4 mb-3">
    <label>HOD Contact</label>
    <input type="text" name="hod_contact"
           value="{{ old('hod_contact',$hod->hod_contact) }}"
           class="form-control @error('hod_contact') is-invalid @enderror">
</div>

<!-- HOD Emails -->
<div class="col-md-12 mb-3">
<label>HOD Emails (Select Primary)</label>

<div id="hod-emails">
@foreach($hod->hodEmails as $i => $mail)
<div class="d-flex align-items-center mb-2 email-row">
    <input type="email"
           name="hod_emails[]"
           value="{{ old('hod_emails.'.$i, $mail->email) }}"
           class="form-control me-2">

    <input type="radio"
           name="hod_primary"
           value="{{ $i }}"
           {{ old('hod_primary', $mail->is_primary ? $i : null) == $i ? 'checked' : '' }}>

    <small class="ms-1">Primary</small>

    <button type="button"
            class="btn btn-sm btn-danger ms-2"
            onclick="removeRow(this)">❌</button>
</div>
@endforeach
</div>

<button type="button" class="btn btn-sm btn-secondary" onclick="addHodEmail()">+ Add More</button>
</div>

</div>
</div>


<!-- ================= TPO ================= -->
<div class="tab-pane fade" id="tpo">
<div class="row">

<h5 class="col-12 mt-4">TPO Details</h5>

<div class="col-md-4 mb-3">
    <label>TPO Name</label>
    <input type="text" name="tpo_name"
           value="{{ old('tpo_name',$hod->tpo_name) }}"
           class="form-control">
</div>

<div class="col-md-4 mb-3">
    <label>TPO Gender</label>
    <select name="tpo_gender" class="form-control">
        <option value="">Select Gender</option>
        <option value="Male" {{ old('tpo_gender',$hod->tpo_gender)=='Male'?'selected':'' }}>Male</option>
        <option value="Female" {{ old('tpo_gender',$hod->tpo_gender)=='Female'?'selected':'' }}>Female</option>
        <option value="Other" {{ old('tpo_gender',$hod->tpo_gender)=='Other'?'selected':'' }}>Other</option>
    </select>
</div>

<div class="col-md-4 mb-3">
    <label>TPO Contact</label>
    <input type="text" name="tpo_contact"
           value="{{ old('tpo_contact',$hod->tpo_contact) }}"
           class="form-control">
</div>

<!-- TPO Emails -->
<div class="col-md-12 mb-3">
<label>TPO Emails (Select Primary)</label>

<div id="tpo-emails">
@foreach($hod->tpoEmails as $i => $mail)
<div class="d-flex align-items-center mb-2 email-row">
    <input type="email" name="tpo_emails[]" value="{{ old('tpo_emails.'.$i,$mail->email) }}"
           class="form-control me-2">

    <input type="radio" name="tpo_primary" value="{{ $i }}"
           {{ old('tpo_primary',$mail->is_primary? $i:null)==$i?'checked':'' }}>
    <small class="ms-1">Primary</small>

    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeRow(this)">❌</button>
</div>
@endforeach
</div>

<button type="button" class="btn btn-sm btn-secondary" onclick="addTpoEmail()">+ Add More</button>
</div>

</div>
</div>

<!-- ================= PRINCIPAL ================= -->
<div class="tab-pane fade" id="principal">
<div class="row">

<h5 class="col-12 mt-3">Principal Details</h5>

<div class="col-md-4 mb-3">
    <label>Name</label>
    <input type="text" name="principal_name"
           value="{{ old('principal_name',$hod->principal_name) }}"
           class="form-control @error('principal_name') is-invalid @enderror">
</div>

<div class="col-md-4 mb-3">
    <label>Gender</label>
    <select name="principal_gender" class="form-control">
        <option value="">Select Gender</option>
        <option value="Male" {{ old('principal_gender',$hod->principal_gender)=='Male'?'selected':'' }}>Male</option>
        <option value="Female" {{ old('principal_gender',$hod->principal_gender)=='Female'?'selected':'' }}>Female</option>
        <option value="Other" {{ old('principal_gender',$hod->principal_gender)=='Other'?'selected':'' }}>Other</option>
    </select>
</div>

<div class="col-md-4 mb-3">
    <label>Contact</label>
    <input type="text" name="principle_contact"
           value="{{ old('principle_contact',$hod->principle_contact) }}"
           class="form-control @error('principle_contact') is-invalid @enderror">
</div>


</div>
</div>


</div>

<button class="btn btn-primary mt-3">Update</button>
<a href="{{ route('hods.index') }}" class="btn btn-secondary mt-3">Back</a>

</form>
</div>

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