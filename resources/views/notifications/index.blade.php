@extends('layouts.app')

@section('content')

<div class="container">

    {{-- Page Heading --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Notifications</h3>

        @if($notifications->count() > 0)
            <form action="{{ route('notifications.markAll') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-secondary">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>


    {{-- Alerts for actions --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Notification List --}}
    @forelse($notifications as $n)
        <div class="card mb-3 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        {{-- Title --}}
                        <h5 class="fw-bold">
                            {{ $n->data['title'] }}
                        </h5>

                        {{-- Message --}}
                        <p class="mb-2 text-muted">
                            {{ $n->data['message'] }}
                        </p>

                        {{-- Status --}}
                        @if($n->read_at)
                            <span class="badge bg-secondary">Read</span>
                        @else
                            <span class="badge bg-success">Unread</span>
                        @endif
                    </div>


                    {{-- View Button --}}
                    <a href="{{ route('notifications.view', $n->id) }}"
                       class="btn btn-primary btn-sm">
                        View
                    </a>
                </div>

            </div>
        </div>
    @empty

        {{-- Empty State --}}
        <div class="alert alert-info text-center">
            No notifications available.
        </div>

    @endforelse


    {{-- Pagination --}}
    <div class="mt-3">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
     
</div>

@endsection
