<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ $jd->title }} | Careers</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f7fb;
    font-family: Arial;
}

/* HEADER */
.header{
    background:#00163e;
    padding:15px 0;
}
.header img{ height:45px; }

/* PAGE TITLE */
.page-title{
    font-size:32px;
    font-weight:700;
    color:#00163e;
}

/* JOB CARD */
.job-card{
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

/* META */
.job-meta{
    font-size:14px;
    color:#555;
}

/* BACK */
.back-link{
    text-decoration:none;
    color:#ff5800;
    font-weight:600;
}

/* APPLY BUTTON */
.apply-btn{
    background:#ff5800;
    color:#fff;
    padding:10px 20px;
    border-radius:8px;
    text-decoration:none;
}

.apply-btn:hover{
    background:#e14e00;
}

</style>

</head>

<body>

<!-- HEADER -->
<div class="header text-center">
    <a href="/">
        <img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/ss-logo.png">
    </a>
</div>

<div class="container my-5">

     

    <h2 class="page-title mt-3">{{ $jd->title }}</h2>

    <div class="job-meta mb-3">
        <span><b>Job Type:</b> {{ ucfirst($jd->job_type) }}</span> |
        <span><b>Apply Before:</b> 
            {{ $jd->last_date ? \Carbon\Carbon::parse($jd->last_date)->format('d M Y') : '-' }}
        </span>
    </div>

    <div class="job-card mt-3">

        {!! $jd->description !!}

    </div>

    <div class="mt-4">
        <button onclick="copyLink()" class="apply-btn">
            Share Job
        </button>
    </div>

</div>

<script>
// function copyLink(){
//     navigator.clipboard.writeText(window.location.href);

//     alert("Job link copied!");
// }

function copyLink(){
    navigator.clipboard.writeText(window.location.href).then(function(){

        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Job link copied successfully',
            timer: 1500,
            showConfirmButton: false
        });

    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>