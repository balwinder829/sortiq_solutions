<!DOCTYPE html>

<html>
<head>
<meta charset="UTF-8">

<style>
body{
    font-family: DejaVu Sans, sans-serif;
    margin:0;
    background:#e0e0e0;
}

/* MAIN CARD */
.wrapper{
    width:800px;
    margin:30px auto;
    background:#ffffff;
}

/* HEADER */
.header{
    background:#2f4154;
    color:#ffffff;
    padding:25px 30px;
}

.header h1{
    margin:0;
    font-size:26px;
}

.header p{
    margin:6px 0 0;
    font-size:14px;
}

/* CONTENT */
.content{
    padding:25px 30px;
}

/* TABLE FOR 2 COLUMNS */
.table{
    width:100%;
    border-collapse:collapse;
}

.left{
    width:50%;
    vertical-align:top;
    padding-right:15px;
}

.right{
    width:50%;
    vertical-align:top;
    padding-left:15px;
}

/* SECTION */
.section{
    margin-bottom:18px;
}

.section-title{
    font-weight:bold;
    font-size:14px;
    margin-bottom:6px;
}

/* TEXT */
p{
    margin:4px 0;
    font-size:12px;
}

ul{
    padding-left:16px;
    margin:5px 0;
}

li{
    font-size:12px;
    margin-bottom:3px;
}

.small{
    font-size:11px;
}

.bold{
    font-weight:bold;
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

<div class="wrapper">


<!-- HEADER -->
<div class="header">
    <h1>{{ $cv->full_name }}</h1>
    <p>{{ $cv->title }}</p>
</div>

<!-- CONTENT -->
<div class="content">

    <table class="table">
        <tr>

            <!-- LEFT SIDE -->
            <td class="left">

                <div class="section  mt-3">
                    <div class="section-title">My Contact</div>
                    <p>{{ $cv->phone }}</p>
                    <p>{{ $cv->email }}</p>
                    <p>{{ $cv->location }}</p>
                </div>

                @if($cv->skills->count())
                <div class="section  mb-2">
                    <div class="section-title">Soft Skill</div>
                    <ul>
                        @foreach($cv->skills as $skill)
                            <li>{{ $skill->skill }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($cv->education->count())
                <div class="section mt-3">
                    <div class="section-title">Education Background</div>

                    @foreach($cv->education as $edu)
                        <p class="small">
                            <span class="bold">{{ $edu->degree }}</span><br>
                            {{ $edu->institution }}<br>
                            {{ $edu->start_year }} - {{ $edu->end_year }}
                        </p>
                    @endforeach

                </div>
                @endif

            </td>

            <!-- RIGHT SIDE -->
            <td class="right">

                <div class="section">
                    <div class="section-title">About Me</div>
                    <p>{{ $cv->summary }}</p>
                </div>

                @if($cv->experience->count())
                <div class="section">
                    <div class="section-title">Experience</div>

                    @foreach($cv->experience as $exp)
                        <p class="bold">{{ $exp->role }}</p>
                        <p class="small">{{ $exp->start_date }} - {{ $exp->end_date }}</p>
                    @endforeach

                </div>
                @endif

                @if($cv->projects->count())
                <div class="section mt-3">
                    <div class="section-title">Projects</div>

                    @foreach($cv->projects as $project)
                        <p class="bold">{{ $project->title }}</p>
                        <p>{{ $project->description }}</p>
                    @endforeach

                </div>
                @endif

            </td>

        </tr>
    </table>

</div>


</div>

</body>
</html>
