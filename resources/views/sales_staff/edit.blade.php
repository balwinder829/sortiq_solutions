@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Mentor</h3>
<!-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif -->
<form method="POST" action="{{ route('sales_staff.update', $sales_staff->id) }}">
<div class="row">
@csrf
@method('PUT')

{{-- Username --}}
<div class="form-group col-md-6">
    <label>UserName</label>
    <input type="text"
        name="username"
        class="form-control"
        value="{{ old('username', $sales_staff->username ?? '') }}"
        >
        @error('username')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

{{-- Full Name --}}
<div class="form-group col-md-6">
    <label>Full Name</label>
    <input type="text"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $sales_staff->name ?? '') }}"
        required>
    @error('name')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

{{-- Password --}}
<div class="form-group col-md-6">
    <label>Password</label>
    <input type="text"
        name="password"
        class="form-control @error('password') is-invalid @enderror"
        placeholder="Leave blank to keep current password">
    @error('password')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

{{-- Existing Password --}}
<div class="form-group col-md-6">
    <label>Existing Password</label>

    <div class="input-group">
        <input type="password"
            id="plain_pswd"
            class="form-control"
            value="{{ old('plain_pswd', $sales_staff->plain_pswd) }}"
            readonly>

        <span class="input-group-text" style="cursor:pointer"
              onclick="toggleProbation()">👁</span>
    </div>
</div>

{{-- Gender --}}
<div class="form-group col-md-6">
    <label>Gender</label>
    <select name="gender"
        class="form-control @error('gender') is-invalid @enderror"
        required>
        <option value="">--Select--</option>
        <option value="male" {{ old('gender',$sales_staff->gender)=='male'?'selected':'' }}>Male</option>
        <option value="female" {{ old('gender',$sales_staff->gender)=='female'?'selected':'' }}>Female</option>
    </select>
    @error('gender')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

{{-- Phone --}}
<div class="form-group col-md-6">
    <label>Phone</label>
    <input type="text"
        name="phone"
        class="form-control @error('phone') is-invalid @enderror"
        value="{{ old('phone',$sales_staff->phone ?? '') }}"
        required
        minlength="10"
        maxlength="10"
        pattern="[0-9]{10}">
    @error('phone')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

{{-- Email --}}
<div class="form-group col-md-6">
    <label>Email</label>
    <input type="email"
        name="email"
        class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email',$sales_staff->email ?? '') }}">
    @error('email')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

{{-- Status --}}
<div class="form-group col-md-6">
    <label>Status</label>
    <select name="status"
        class="form-control @error('status') is-invalid @enderror"
        required>
        <option value="">Select Status</option>
        <option value="active" {{ $sales_staff->status=='active'?'selected':'' }}>Active</option>
        <option value="inactive" {{ $sales_staff->status=='inactive'?'selected':'' }}>Inactive</option>
    </select>
    @error('status')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group col-md-12">
    <button type="submit" class="btn btn-primary mt-3">Update</button>
    <a href="{{ route('sales_staff.index') }}" class="btn btn-secondary mt-3 ml-2">Back</a>
</div>

</div>
</form>
</div>

<script>
function toggleProbation() {
    const input = document.getElementById('plain_pswd');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
