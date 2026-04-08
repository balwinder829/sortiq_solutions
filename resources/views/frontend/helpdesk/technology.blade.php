<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ $technology->name }} | Helpdesk</title>

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

/* ARTICLE CARD */
.article-card{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
    transition:.3s;
}

.article-card:hover{
    transform:translateY(-5px);
}

.article-card a{
    text-decoration:none;
    color:#00163e;
    font-weight:600;
    font-size:18px;
}

.article-card a:hover{
    color:#ff5800;
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

    <a href="{{ route('helpdesk.index') }}" class="back-link">
        ← Back to Helpdesk
    </a>

    <h2 class="page-title mt-3">{{ $technology->name }}</h2>

    <hr>

    <div class="row">

        @forelse($articles as $article)

            <div class="col-md-6 mb-3">
                <div class="article-card">

                    <a href="{{ route('helpdesk.article', [$technology->slug, $article->slug]) }}">
                        {{ $article->title }}
                    </a>

                </div>
            </div>

        @empty

            <p>No articles available.</p>

        @endforelse

    </div>

</div>

</body>
</html>