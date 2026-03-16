@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Project Details</h1>

<div class="card">

<div class="card-body">

<h4>{{ $project->title }}</h4>

<p>
<strong>Type:</strong>
{{ ucfirst($project->project_type) }}
</p>

<p>
<strong>Technology:</strong>
{{ $project->technology }}
</p>

<p>
<strong>Difficulty:</strong>
{{ ucfirst($project->difficulty) }}
</p>

<p>
<strong>Estimated Days:</strong>
{{ $project->estimated_days }}
</p>

<p>
<strong>Description:</strong>
</p>

<p>
{{ $project->description }}
</p>

</div>

</div>

</div>

@endsection