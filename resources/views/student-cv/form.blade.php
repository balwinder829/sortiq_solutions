<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CV Builder | Sortiq Solutions</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
font-family:system-ui;
}

/* HEADER */

.site-header{
background:#00163e;
padding:15px 0;
}

.site-header img{
height:45px;
}

/* PAGE TITLE */

.page-title{
font-size:32px;
font-weight:700;
color:#00163e;
margin-bottom:25px;
}

/* FORM CARD */

.form-card{
background:#fff;
padding:30px;
border-radius:14px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
margin-bottom:25px;
}

.form-card h4{
font-size:20px;
font-weight:600;
margin-bottom:20px;
}

/* INPUTS */

form input,
form textarea{
width:100%;
border:1px solid #ddd;
border-radius:10px;
padding:12px 14px;
font-size:15px;
}

form textarea{
height:120px;
resize:none;
}

form input:focus,
form textarea:focus{
outline:none;
border-color:#ff5800;
box-shadow:0 0 0 3px rgba(255,88,0,.15);
}

/* ADD BUTTON */

.add-btn{
background:#eef2ff;
border:none;
padding:6px 12px;
font-size:13px;
border-radius:6px;
}

/* SUBMIT */

.submit-btn{
background:#ff5800;
border:none;
padding:14px;
width:100%;
font-size:18px;
border-radius:10px;
color:#fff;
font-weight:600;
}

/* FOOTER */

footer{
text-align:center;
padding:20px;
font-size:14px;
color:#777;
}

</style>

</head>

<body>

<header class="site-header">
<div class="container d-flex align-items-center">
<a href="/">
<img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/ss-logo.png">
</a>
</div>
</header>

<div class="container py-5">

<h1 class="page-title text-center">
Create Your Professional Resume
</h1>

<form method="POST" action="{{ route('student.cv.store') }}">
@csrf


<!-- PERSONAL INFO -->

<div class="form-card">

<h4>Personal Information</h4>

<div class="row g-3">

<div class="col-md-6">
<input type="text" name="full_name" placeholder="Full Name" required>
</div>

<div class="col-md-6">
<input type="text" name="title" placeholder="Professional Title">
</div>

<div class="col-md-6">
<input type="email" name="email" placeholder="Email">
</div>

<div class="col-md-6">
<input type="text" name="phone" placeholder="Phone">
</div>

<div class="col-md-6">
<input type="text" name="location" placeholder="Location">
</div>

<div class="col-md-6">
<input type="text" name="linkedin" placeholder="LinkedIn URL">
</div>

<div class="col-md-6">
<input type="text" name="github" placeholder="GitHub">
</div>

<div class="col-md-6">
<input type="text" name="portfolio" placeholder="Portfolio Website">
</div>

<div class="col-md-12">
<textarea name="summary" placeholder="Professional Summary"></textarea>
</div>

</div>

</div>


<!-- SKILLS -->

<div class="form-card">

<h4>
Skills
<button type="button" class="add-btn" onclick="addSkill()">+ Add</button>
</h4>

<div id="skills">

<input type="text" name="skills[]" placeholder="Skill">

</div>

</div>


<!-- EDUCATION -->

<div class="form-card">

<h4>
Education
<button type="button" class="add-btn" onclick="addEducation()">+ Add</button>
</h4>

<div id="education">

<div class="row g-2 mb-2">

<div class="col-md-4">
<input type="text" name="degree[]" placeholder="Degree">
</div>

<div class="col-md-4">
<input type="text" name="institution[]" placeholder="Institution">
</div>

<div class="col-md-2">
<input type="text" name="start_year[]" placeholder="Start Year">
</div>

<div class="col-md-2">
<input type="text" name="end_year[]" placeholder="End Year">
</div>

</div>

</div>

</div>


<!-- PROJECTS -->

<div class="form-card">

<h4>
Projects
<button type="button" class="add-btn" onclick="addProject()">+ Add</button>
</h4>

<div id="projects">

<div class="mb-3">

<input type="text" name="project_title[]" placeholder="Project Title">

<textarea name="project_description[]" placeholder="Project Description"></textarea>

<input type="text" name="project_tech[]" placeholder="Technologies">

<input type="text" name="github_link[]" placeholder="GitHub Link">

</div>

</div>

</div>


<!-- EXPERIENCE -->

<div class="form-card">

<h4>
Experience
<button type="button" class="add-btn" onclick="addExperience()">+ Add</button>
</h4>

<div id="experience">

<div class="row g-2">

<div class="col-md-4">
<input type="text" name="company_name[]" placeholder="Company">
</div>

<div class="col-md-4">
<input type="text" name="role[]" placeholder="Role">
</div>

<div class="col-md-2">
<input type="date" name="exp_start[]">
</div>

<div class="col-md-2">
<input type="date" name="exp_end[]">
</div>

</div>

<textarea name="exp_summary[]" placeholder="Work Summary"></textarea>

</div>

</div>


<!-- CERTIFICATIONS -->

<div class="form-card">

<h4>
Certifications
<button type="button" class="add-btn" onclick="addCertification()">+ Add</button>
</h4>

<div id="certifications">

<div class="row g-2">

<div class="col-md-6">
<input type="text" name="cert_title[]" placeholder="Certification">
</div>

<div class="col-md-4">
<input type="text" name="issuer[]" placeholder="Issuer">
</div>

<div class="col-md-2">
<input type="text" name="cert_year[]" placeholder="Year">
</div>

</div>

</div>

</div>
<div class="form-card">

<h4>Select Resume Template</h4>

<div class="row">

@foreach($templates as $template)

<div class="col-md-4">

<label class="template-box">

<input type="radio"
name="template_key"
value="{{ $template->template_key }}"
required>

<div class="template-card">

<img src="{{ asset('uploads/cv-samples/'.$template->sample_cv) }}" width="100%">

<p class="mt-2 text-center">
{{ $template->name }}
</p>

</div>

</label>

</div>

@endforeach

</div>

</div>

<button type="submit" class="submit-btn">
Generate CV
</button>

</form>

</div>


<footer>

© 2026 Sortiq Solutions Pvt Ltd

</footer>


<script>

function addSkill(){
document.getElementById('skills')
.insertAdjacentHTML('beforeend',
'<input type="text" name="skills[]" placeholder="Skill" class="mt-2">');
}

function addEducation(){

let html = `
<div class="row g-2 mt-2">

<div class="col-md-4">
<input type="text" name="degree[]" placeholder="Degree">
</div>

<div class="col-md-4">
<input type="text" name="institution[]" placeholder="Institution">
</div>

<div class="col-md-2">
<input type="text" name="start_year[]" placeholder="Start Year">
</div>

<div class="col-md-2">
<input type="text" name="end_year[]" placeholder="End Year">
</div>

</div>`;

document.getElementById('education')
.insertAdjacentHTML('beforeend',html);

}

function addProject(){

let html = `
<div class="mt-3">

<input type="text" name="project_title[]" placeholder="Project Title">

<textarea name="project_description[]" placeholder="Project Description"></textarea>

<input type="text" name="project_tech[]" placeholder="Technologies">

<input type="text" name="github_link[]" placeholder="GitHub Link">

</div>`;

document.getElementById('projects')
.insertAdjacentHTML('beforeend',html);

}

function addExperience(){

let html = `
<div class="row g-2 mt-2">

<div class="col-md-4">
<input type="text" name="company_name[]" placeholder="Company">
</div>

<div class="col-md-4">
<input type="text" name="role[]" placeholder="Role">
</div>

<div class="col-md-2">
<input type="date" name="exp_start[]">
</div>

<div class="col-md-2">
<input type="date" name="exp_end[]">
</div>

</div>

<textarea name="exp_summary[]" placeholder="Work Summary"></textarea>`;

document.getElementById('experience')
.insertAdjacentHTML('beforeend',html);

}

function addCertification(){

let html = `
<div class="row g-2 mt-2">

<div class="col-md-6">
<input type="text" name="cert_title[]" placeholder="Certification">
</div>

<div class="col-md-4">
<input type="text" name="issuer[]" placeholder="Issuer">
</div>

<div class="col-md-2">
<input type="text" name="cert_year[]" placeholder="Year">
</div>

</div>`;

document.getElementById('certifications')
.insertAdjacentHTML('beforeend',html);

}

</script>

</body>
</html>
