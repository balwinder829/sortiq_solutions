@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Add New Lead</h3>

    <form action="{{ route('leads.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>

            <div class="col-md-4">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>

            <div class="col-md-4">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
        </div>

        <div class="row mb-3">

            <div class="col-md-4">
                <label>Source</label>
                <input type="text" name="source" class="form-control" value="{{ old('source', 'Excel') }}">
            </div>

            <div class="col-md-4">
                <label>Assign To</label>
                <select name="assigned_to" class="form-control">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                            {{ $user->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Status</label>
                <select name="status" class="form-control">
                    @foreach(['new','contacted','follow_up','not_interested','onboarded'] as $st)
                        <option value="{{ $st }}">{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
        </div>

        <button class="btn btn-primary">Save Lead</button>
    </form>

</div>
@endsection
