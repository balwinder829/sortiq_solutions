@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading">{{ $jd->title }}</h1>

<div class="mb-2">
    <b>Job Type:</b> {{ ucfirst($jd->job_type) }}
</div>

<div class="mb-2">
    <b>Status:</b> {{ ucfirst($jd->status) }}
</div>

<div class="mb-2">
    <b>Last Date:</b> {{ $jd->last_date }}
</div>

<hr>

<div>
    {!! $jd->description !!}
</div>

</div>

@endsection