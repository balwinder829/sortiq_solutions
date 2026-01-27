<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $page->meta_title ?? $page->title }}</title>

    @if(!empty($page->meta_description))
        <meta name="description" content="{{ $page->meta_description }}">
    @endif

    @if(!empty($page->meta_keywords))
        <meta name="keywords" content="{{ $page->meta_keywords }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- HEADER -->
<header class="py-3 border-bottom">
    <div class="container d-flex align-items-center">
        <a href="/">
            <img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/ss-logo.png"
                 alt="Sortiq Solutions"
                 height="45">
        </a>
    </div>
</header>

<!-- PAGE CONTENT -->
<main class="container py-5">

    <h1 class="mb-4">{{ $page->title }}</h1>

    <div class="page-content">
        {!! $page->content !!}
    </div>

</main>

<!-- FOOTER -->
<footer class="py-4 text-center border-top">
    <p class="mb-0">
        © {{ date('Y') }} Sortiq Solutions Pvt. Ltd. | All Rights Reserved.
    </p>
</footer>

</body>
</html>
