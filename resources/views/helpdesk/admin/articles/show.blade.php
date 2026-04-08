@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.helpdesk.articles.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>
    <h2>{{ $article->title }}</h2>

    <p><strong>Category:</strong> {{ $article->technology->name ?? '' }}</p>
    <p><strong>Status:</strong> {{ ucfirst($article->status) }}</p>
    <p><strong>View Status:</strong> {{ $article->is_active ? 'Enabled' : 'Disabled' }}</p>
    <p><strong>Expiry:</strong> {{ $article->expires_at ?? 'No Expiry' }}</p>

    <hr>

    <div>
        {!! $article->description !!}
    </div>

    <hr>

    <h5>Attachments</h5>
    <ul>
        @foreach($article->attachments as $file)
            <li>
                <a href="{{ route('admin.helpdesk.attachments.preview', $file->id) }}" target="_blank" style="color:#0d6efd; text-decoration:none;"
   onmouseover="this.style.color='#0a58ca'"
   onmouseout="this.style.color='#0d6efd'">
                    {{ $file->file_name }}
                </a>
            </li>
        @endforeach
    </ul>

</div>
@endsection