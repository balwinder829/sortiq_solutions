@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h1 class="text-danger">403 - Unauthorized</h1>
    <p>You are not authorized to view this page.</p>
    <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Go Back</a>
</div>
@endsection
