@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @foreach($scanners as $scanner)
            <div class="col-md-12 mb-4 text-center">
                <h5>{{ $scanner->name }}</h5>
                <img src="{{ asset('storage/'.$scanner->image_path) }}"
                     class="img-fluid rounded shadow">
            </div>
        @endforeach
    </div>
</div>
@endsection
