<!DOCTYPE html>
<html>
<head>
    <title>Scanners</title>
    <meta name="viewport" content="width=device-width, note-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-4">

    <h2 class="text-center mb-4">Scanners</h2>

    <div class="row g-4">
        @forelse($scanners as $scanner)
            <div class="col-md-4 col-sm-6">

                <div class="card h-100 shadow-sm">
                    <img src="{{ asset($scanner->image_path) }}"
                         class="card-img-top"
                         style="height:200px; object-fit:cover;">

                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $scanner->name }}</h5>

                        @if($scanner->description)
                            <p class="text-muted">
                                {{ \Illuminate\Support\Str::limit($scanner->description, 80) }}
                            </p>
                        @endif

                        <a href="{{ route('scanners.share', $scanner->share_token) }}"
                           class="btn btn-primary btn-sm">
                            View
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-12 text-center">
                <p>No scanners available.</p>
            </div>
        @endforelse
    </div>

</div>

</body>
</html>
