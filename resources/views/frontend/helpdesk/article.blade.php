<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ $article->title }} | Helpdesk</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    font-family: Arial;
    background:#f5f7fb;
}

/* HEADER */
.header{
    background:#00163e;
    padding:15px 0;
}
.header img{ height:45px; }

/* ARTICLE */
.article-box{
    background:#fff;
    padding:35px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.article-title{
    font-size:32px;
    font-weight:700;
    color:#00163e;
}

.article-meta{
    font-size:14px;
    color:#777;
}

/* CONTENT */
.article-content{
    margin-top:20px;
    line-height:1.7;
    font-size:16px;
}

/* ATTACHMENTS */
.attachments{
    margin-top:30px;
}

.attachments a{
    display:block;
    padding:10px 15px;
    background:#f1f3f8;
    border-radius:8px;
    margin-bottom:8px;
    text-decoration:none;
    color:#00163e;
    font-weight:500;
}

.attachments a:hover{
    background:#ff5800;
    color:#fff;
}

/* BACK */
.back-link{
    text-decoration:none;
    color:#ff5800;
    font-weight:600;
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

    <!-- <a href="{{ route('helpdesk.technology',$article->technology->slug) }}" class="back-link">
        ← Back to {{ $article->technology->name }}
    </a> -->

    <div class="article-box mt-3">

        <div class="article-title">
            {{ $article->title }}
        </div>

        <div class="article-meta mt-2">
            Category: {{ $article->technology->name ?? '' }}
        </div>

        <hr>

        {{-- CONTENT --}}
        <div class="article-content">
            {!! $article->description !!}
        </div>

        {{-- ATTACHMENTS --}}
        @if($article->attachments->count())
            <div class="attachments">
                <h5>Attachments</h5>

                @foreach($article->attachments as $file)
                    <a href="{{ route('attachments.preview', $file->id) }}" target="_blank">
                        📄 {{ $file->file_name }}
                    </a>
                @endforeach
            </div>
        @endif

    </div>

</div>

</body>
</html>