<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

@if(!$isPdf)
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
@endif

<style>

body{
font-family:Arial;
font-size:14px;
color:#000;
}

.cv{
max-width:900px;
margin:auto;
}

h1{
font-size:28px;
margin-bottom:5px;
}

.section{
margin-top:25px;
}

.section-title{
font-weight:bold;
border-bottom:1px solid #000;
margin-bottom:10px;
padding-bottom:4px;
}

</style>

</head>

<body>

@if(!$isPdf)
<div style="text-align:right;margin-bottom:20px">

<a href="{{ route('student.cv.download',[$cv->id,$cv->template_key ?: 'classic']) }}"
class="btn btn-success">
Download PDF
</a>

</div>
@endif


<div class="cv">

<h1>{{ $cv->full_name }}</h1>

<p>
{{ $cv->title }}
</p>

<p>
{{ $cv->email }} |
{{ $cv->phone }} |
{{ $cv->location }}
</p>


<div class="section">
<div class="section-title">Summary</div>
<p>{{ $cv->summary }}</p>
</div>


@if($cv->skills->count())
<div class="section">
<div class="section-title">Skills</div>
<ul>
@foreach($cv->skills as $skill)
<li>{{ $skill->skill }}</li>
@endforeach
</ul>
</div>
@endif


@if($cv->projects->count())
<div class="section">
<div class="section-title">Projects</div>

@foreach($cv->projects as $project)

<p>
<strong>{{ $project->title }}</strong><br>
{{ $project->description }}
</p>

@endforeach

</div>
@endif


@if($cv->education->count())
<div class="section">
<div class="section-title">Education</div>

@foreach($cv->education as $edu)

<p>

<strong>{{ $edu->degree }}</strong><br>
{{ $edu->institution }}<br>
{{ $edu->start_year }} - {{ $edu->end_year }}

</p>

@endforeach

</div>
@endif


@if($cv->experience->count())
<div class="section">
<div class="section-title">Experience</div>

@foreach($cv->experience as $exp)

<p>

<strong>{{ $exp->company }}</strong><br>
{{ $exp->role }}<br>
{{ $exp->start_date }} - {{ $exp->end_date }}

</p>

@endforeach

</div>
@endif

</div>

</body>
</html>

