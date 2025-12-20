@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Edit Lead</h3>

    <form action="{{ route('leads.update', $lead->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Name</label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name', $lead->name) }}">
            </div>

            <div class="col-md-4">
                <label>Email</label>
                <input type="email" name="email" class="form-control" 
                       value="{{ old('email', $lead->email) }}">
            </div>

            <div class="col-md-4">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" 
                       value="{{ old('phone', $lead->phone) }}">
            </div>
        </div>

        <div class="row mb-3">

            <div class="col-md-4">
                <label>Source</label>
                <input type="text" name="source" class="form-control"
                       value="{{ old('source', $lead->source) }}">
            </div>

            <div class="col-md-4">
                <label>Assign To</label>
                <select name="assigned_to" class="form-control">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                            {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>
                            {{ $user->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Status</label>
                <select name="status" class="form-control">
                    @foreach(['new','contacted','follow_up','not_interested','onboarded'] as $st)
                        <option value="{{ $st }}" 
                                {{ old('status', $lead->status) == $st ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_',' ', $st)) }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Next Follow-up Date</label>
                <input type="date" name="follow_up_date" class="form-control"
                       value="{{ old('follow_up_date', optional($lead->follow_up_date)->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $lead->notes) }}</textarea>
        </div>

        <button class="btn btn-primary">Update Lead</button>
        <a href="{{ route('leads.index') }}" class="btn btn-secondary">Cancel</a>
    </form>

</div>
@endsection
