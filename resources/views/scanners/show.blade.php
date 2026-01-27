@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Scanner Details</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('scanners.edit', $scanner) }}" class="btn btn-primary">
                Edit
            </a>
            <a href="{{ route('scanners.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="row">

                {{-- Image --}}
                <div class="col-md-6 text-center mb-3">
                    <img src="{{ asset($scanner->image_path) }}"
                         class="img-fluid rounded shadow"
                         style="max-height:300px;">
                </div>

                {{-- Details --}}
                <div class="col-md-6">

                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name</th>
                            <td>{{ $scanner->name }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge {{ $scanner->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $scanner->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Visibility</th>
                            <td>
                                <span class="badge {{ $scanner->is_public ? 'bg-info' : 'bg-dark' }}">
                                    {{ $scanner->is_public ? 'Public' : 'Private' }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Source</th>
                            <td>
                                {{ ucfirst($scanner->source ?? 'manual') }}
                            </td>
                        </tr>

                        @if($scanner->source_url)
                        <tr>
                            <th>Source URL</th>
                            <td>
                                <a href="{{ $scanner->source_url }}"
                                   target="_blank">
                                    {{ $scanner->source_url }}
                                </a>
                            </td>
                        </tr>
                        @endif

                        <tr>
                            <th>Created At</th>
                            <td>{{ $scanner->created_at->format('d M Y, h:i A') }}</td>
                        </tr>

                        <tr>
                            <th>Updated At</th>
                            <td>{{ $scanner->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>

                        @if($scanner->share_token)
                        <tr>
                            <th>Share Link</th>
                            <td>
                                <input type="text"
                                       class="form-control"
                                       value="{{ route('scanners.share', $scanner->share_token) }}"
                                       readonly
                                       onclick="this.select();">
                            </td>
                        </tr>
                        @endif
                    </table>

                </div>

            </div>

            {{-- Description --}}
            @if($scanner->description)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Description</h5>
                    <div class="border rounded p-3">
                        {!! nl2br(e($scanner->description)) !!}
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
