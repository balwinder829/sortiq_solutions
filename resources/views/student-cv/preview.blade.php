@extends('layouts.app')

@section('content')

<div class="container">

<h1>{{ $cv->full_name }}</h1>

<p>{{ $cv->title }}</p>

<h3>Summary</h3>

<p>{{ $cv->summary }}</p>

<h3>Skills</h3>

<ul>

@foreach($cv->skills as $skill)

<li>{{ $skill->skill }}</li>

@endforeach

</ul>

<h3>Download CV</h3>

@foreach($templates as $template)

<a href="{{ route('admin.student.cv.download',[$cv->id,$template->template_key]) }}"
class="btn btn-success mb-2">

Download {{ $template->name }}

</a>

@endforeach

</div>

@endsection