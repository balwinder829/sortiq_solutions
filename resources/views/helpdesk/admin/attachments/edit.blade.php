@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page_heading">Edit Attachment</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.helpdesk.attachments.update',$attachment->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6 mb-3">
                <label>Article</label>
                <select name="article_id" class="form-control">
                    @foreach($articles as $id=>$title)
                        <option value="{{ $id }}"
                            {{ $attachment->article_id == $id ? 'selected':'' }}>
                            {{ $title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>Replace File (optional)</label>
                <input type="file" name="file" class="form-control">
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>Expire At</label>
                <input type="datetime-local"
                       name="expires_at"
                       value="{{ $attachment->expires_at ? \Carbon\Carbon::parse($attachment->expires_at)->format('Y-m-d\TH:i') : '' }}"
                       class="form-control">
            </div>

        </div>

        <button class="btn btn-primary">Update Attachment</button>

    </form>

</div>

@endsection
