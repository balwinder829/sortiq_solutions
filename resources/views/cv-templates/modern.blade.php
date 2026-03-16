<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<style>

body{
    font-family: system-ui, sans-serif;
    margin:0;
}

/* page container */
.container{
    width:900px;
    margin:40px auto;
}

/* table layout for PDF compatibility */
.cv-table{
    width:100%;
    border-collapse:collapse;
}

/* LEFT SIDEBAR */
.left{
    width:32%;
    background:#34495e;
    color:#fff;
    padding:30px;
    vertical-align:top;
}

/* RIGHT CONTENT */
.right{
    width:68%;
    padding:30px 40px;
    vertical-align:top;
}

h2{
    margin:0 0 5px 0;
}

.title{
    margin-bottom:25px;
}

.section{
    margin-top:25px;
    page-break-inside: avoid;
}

.section-title{
    font-weight:bold;
    border-bottom:2px solid #ddd;
    margin-bottom:10px;
    padding-bottom:5px;
}

ul{
    padding-left:18px;
    margin:0;
}

p{
    margin:6px 0;
}
body{
    font-family: DejaVu Sans, sans-serif;
    margin:0;
    font-size:13px;
    line-height:1.6;
}

/* container */
.container{
    width:900px;
    margin:40px auto;
}

/* layout table */
.cv-table{
    width:100%;
    border-collapse:collapse;
}

/* sidebar */
.left{
    width:32%;
    background:#34495e;
    color:#fff;
    padding:35px;
    vertical-align:top;
}

/* right content */
.right{
    width:68%;
    padding:35px 40px;
    vertical-align:top;
}

/* headings */
h2{
    margin:0 0 8px 0;
    font-size:22px;
}

.title{
    margin-bottom:25px;
    font-size:14px;
}

.section{
    margin-top:25px;
    page-break-inside: avoid;
}

.section-title{
    font-weight:bold;
    font-size:15px;
    border-bottom:2px solid #ddd;
    padding-bottom:5px;
    margin-bottom:12px;
}

/* text spacing */
p{
    margin:8px 0;
}

/* skills list */
ul{
    margin:8px 0;
    padding-left:18px;
}

li{
    margin-bottom:4px;
}
</style>

</head>

<body>

@if(!$isPdf)
<div style="text-align:right;padding:15px">

<a href="{{ route('student.cv.download',[$cv->id,$cv->template_key ?: 'classic']) }}"
style="background:#2c3e50;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none">
Download PDF
</a>

</div>
@endif


<div class="container">

<table class="cv-table">
<tr>

<!-- LEFT SIDE -->
<td class="left">

<h2>{{ $cv->full_name }}</h2>

<div class="title">
{{ $cv->title }}
</div>

<p>{{ $cv->email }}</p>
<p>{{ $cv->phone }}</p>
<p>{{ $cv->location }}</p>


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

</td>


<!-- RIGHT SIDE -->
<td class="right">

<div class="section">
<div class="section-title">Profile</div>
<p>{{ $cv->summary }}</p>
</div>


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

</td>

</tr>
</table>

</div>

</body>
</html>