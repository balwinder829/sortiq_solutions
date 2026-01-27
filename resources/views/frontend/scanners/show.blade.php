<!DOCTYPE html>
<html>
<head>
    <title>{{ $scanner->name }}</title>
    <meta name="viewport" content="width=device-width, note-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-4">

    <div class="row justify-content-center">
        <div class="col-md-8 text-center">

            <h2 class="mb-3">{{ $scanner->name }}</h2>

            <img src="{{ asset($scanner->image_path) }}"
                 class="img-fluid rounded shadow mb-3">

            @if($scanner->description)
                <p class="mt-3">
                    {{ $scanner->description }}
                </p>
            @endif

            @if($scanner->source_url)
                <a href="{{ $scanner->source_url }}"
                   target="_blank"
                   class="btn btn-outline-secondary mt-2">
                    Visit Source
                </a>
            @endif

            <div class="mt-4">
                <a href="{{ route('frontend.scanners.index') }}"
                   class="btn btn-secondary">
                    Back to All Scanners
                </a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
