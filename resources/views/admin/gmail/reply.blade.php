@extends('layouts.app')

@section('title', 'Reply Email')

@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <h4>Reply – {{ $account === 'hr' ? 'HR' : 'Queries' }} Gmail</h4>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.gmail.index', array_merge(['account' => $account], $listFilters ?? [])) }}" class="btn btn-sm btn-secondary">
                Back to list
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('admin.gmail.reply.send', ['account' => $account, 'uid' => $uid]) }}">
                @csrf
                <input type="hidden" name="return_folder" value="{{ $folder }}">
                <input type="hidden" name="return_filter" value="{{ $listFilters['filter'] ?? '' }}">
                <input type="hidden" name="return_q" value="{{ $listFilters['q'] ?? '' }}">
                <input type="hidden" name="return_subject_contains" value="{{ $listFilters['subject'] ?? '' }}">
                <input type="hidden" name="return_limit" value="{{ $listFilters['limit'] ?? '' }}">

                <div class="mb-3">
                    <label class="form-label">To</label>
                    <input type="email" name="to" class="form-control" value="{{ old('to', $to_email) }}" required>
                    @error('to')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject', $subject) }}" required>
                    @error('subject')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="body" rows="8" class="form-control" required>{{ old('body') }}</textarea>
                    @error('body')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                @if($original_snippet)
                    <div class="mb-3">
                        <label class="form-label">Original message (preview)</label>
                        <div class="border rounded p-2 bg-light small">
                            {{ $original_snippet }}
                        </div>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary">Send Reply</button>
            </form>
        </div>
    </div>
@endsection

