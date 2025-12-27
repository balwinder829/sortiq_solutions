@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Blocked Numbers</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('admin.blocked-numbers.create') }}"
       class="btn btn-primary mb-3">
        Block New Number
    </a>
            </div>
        </div>
    </div>
     

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Number</th>
            <th>Occurrences</th>
            <th>Blocked At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($blockedNumbers as $blocked)
            <tr>
                <td>{{ $blocked->number }}</td>
                <td>{{ $blocked->occurrence_count }}</td>
                <td>{{ \Carbon\Carbon::parse($blocked->blocked_at)->format('d M Y h:i A') }}</td>
                <td>
                    <a href="{{ route('admin.blocked-numbers.show', $blocked) }}"
                       class="btn btn-sm btn-info">View</a>

                   <!--  <form method="POST"
                          action="{{ route('admin.blocked-numbers.destroy', $blocked) }}"
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger"
                                onclick="return confirm('Unblock this number?')">
                            Unblock
                        </button>
                    </form> -->
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $blockedNumbers->links() }}
</div>
@endsection
