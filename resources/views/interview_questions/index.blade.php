@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Interview Questions</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('interview-questions.practice') }}"
                   class="btn" style="background-color:#6b51df;color:#fff;">
                    View Q&As
                </a>
                <a href="{{ route('interview-questions.create') }}?{{ http_build_query(request()->query()) }}"
                   class="btn" style="background-color:#6b51df;color:#fff;">
                    Add Question
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-10">
            <form method="GET" id="filterForm" class="row g-2">

                <div class="col-md-3">
                    <select name="round_type" class="form-control filterchange">
                        <option value="">All Rounds</option>
                        <option value="hr" {{ request('round_type')=='hr'?'selected':'' }}>HR</option>
                        <option value="technical" {{ request('round_type')=='technical'?'selected':'' }}>Technical</option>
                        <option value="machine" {{ request('round_type')=='machine'?'selected':'' }}>Machine</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="experience_level" class="form-control filterchange">
                        <option value="">All Experience</option>
                        <option value="fresher" {{ request('experience_level')=='fresher'?'selected':'' }}>Fresher</option>
                        <option value="1-3" {{ request('experience_level')=='1-3'?'selected':'' }}>1–3 Years</option>
                        <option value="3-5" {{ request('experience_level')=='3-5'?'selected':'' }}>3–5 Years</option>
                        <option value="5+" {{ request('experience_level')=='5+'?'selected':'' }}>5+ Years</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="technology_id" class="form-control filterchange">
                        <option value="">All Technologies</option>
                        @foreach($technologies as $tech)
                            <option value="{{ $tech->id }}"
                                {{ request('technology_id')==$tech->id?'selected':'' }}>
                                {{ $tech->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <!-- <button class="btn btn-primary">Search</button> -->
                    <a href="{{ route('interview-questions.index') }}" class="btn btn-secondary">Reset</a>
                </div>

            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="questionsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Round</th>
                <th>Question</th>
                <th>Experience</th>
                <th>Technology</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($questions as $q)
            <tr>
                <td></td>
                <td>
                    <span class="badge bg-info">
                        {{ strtoupper($q->round_type) }}
                    </span>
                </td>
                <td>{{ $q->question }}</td>
                <td>{{ $q->experience_level }}</td>
                <td>{{ $q->technology->name ?? '-' }}</td>
                <td>
                    <!-- <a href="{{ route('interview-questions.edit', $q) }}"
                       class="btn btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a> -->

                    <a href="{{ route('interview-questions.edit', $q) }}?{{ http_build_query(request()->query()) }}"
                       class="btn btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    <form action="{{ route('interview-questions.destroy', $q) }}"
                          method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm" title="Delete"
                                data-swal-confirm="Delete this question?">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection

@push('styles')
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#questionsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        columnDefs: [
                {
                    targets: 0, // first column
                    searchable: false,
                    orderable: false
                }
            ]
        });

        table.on('draw.dt', function () {
            var PageInfo = table.page.info();

            table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = PageInfo.start + i + 1;
            });
        }).draw();
});
</script>
<script>
$(document).ready(function(){

    let timer;

    $('.filterchange').on('change', function(){
        $('#filterForm').submit();
        
    });
    $('.filterchangetext').on('input', function(){
        clearTimeout(timer);

        timer = setTimeout(function(){
            $('#filterForm').submit();
        }, 500); // waits 500ms after typing stops
    });

});
</script>
@endpush
