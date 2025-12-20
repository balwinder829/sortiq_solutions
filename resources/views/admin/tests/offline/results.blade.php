@extends('layouts.app')

@section('content')
<div class="container my-4">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <a href="{{ route('admin.offline-tests.index') }}"
               class="btn btn-outline-secondary btn-sm mb-2">
                ← Back
            </a>

            <h3 class="mb-1">
                {{ $test->title }}
                <span class="badge bg-info">Offline</span>
            </h3>

            <h6 class="text-muted mb-0">
                College : {{ $test->college->full_name ?? '-' }}
            </h6>
        </div>

        <a href="{{ route('admin.offline.tests.create.student', $test->id) }}"
           class="btn btn-primary">
            ➕ Add Student
        </a>
    </div>

    {{-- ================= SUCCESS MESSAGE ================= --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================= FILTERS ================= --}}
   <form method="GET"
      action="{{ route('admin.offline-tests.results', $test->id) }}"
      class="row g-2 mb-4">

    <div class="col-md-2">
        <input type="text"
               name="sno"
               value="{{ request('sno') }}"
               class="form-control"
               placeholder="S.No">
    </div>

    <div class="col-md-2">
        <input type="text"
               name="name"
               value="{{ request('name') }}"
               class="form-control"
               placeholder="Student Name">
    </div>

    <div class="col-md-2">
        <input type="text"
               name="email"
               value="{{ request('email') }}"
               class="form-control"
               placeholder="Email">
    </div>

    <div class="col-md-2">
        <input type="number"
               name="top_n"
               value="{{ request('top_n') }}"
               class="form-control"
               placeholder="Top N">
    </div>

    <div class="col-md-2">
        <select name="finalized" class="form-select">
            <option value="">All</option>
            <option value="1" {{ request('finalized')==='1'?'selected':'' }}>
                Selected
            </option>
            <option value="0" {{ request('finalized')==='0'?'selected':'' }}>
                Not Selected
            </option>
        </select>
    </div>

    <div class="col-md-2">
        <select name="moved" class="form-select">
            <option value="">All</option>
            <option value="1" {{ request('moved')==='1' ? 'selected' : '' }}>
                Moved to Enquiries
            </option>
            <option value="0" {{ request('moved')==='0' ? 'selected' : '' }}>
                Not Moved
            </option>
        </select>
    </div>

    <div class="col-md-1">
    <button class="btn btn-primary w-100">Go</button>
</div>

<div class="col-md-1 ms-2">
    <a href="{{ route('admin.offline-tests.results', $test->id) }}"
       class="btn btn-secondary w-100">
        Reset
    </a>
</div>


</form>

<div class="d-flex gap-2 mb-3">

    {{-- DOWNLOAD ALL OFFLINE STUDENTS --}}
    <a href="{{ route('admin.tests.export.all', $test->id) }}"
       class="btn btn-outline-primary">
        <i class="fa fa-download"></i> Download All
    </a>

    {{-- DOWNLOAD FINALIZED OFFLINE STUDENTS --}}
    <a href="{{ route('admin.tests.export.finalized', $test->id) }}"
       class="btn btn-outline-success">
        <i class="fa fa-download"></i> Download Selected
    </a>

</div>

@if($errors->any())
<div class="alert alert-danger">
    <strong>Import Errors:</strong>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


    {{-- ================= UPLOAD EXCEL ================= --}}
    {{-- ================= UPLOAD EXCEL ================= --}}
<div class="card mb-3 shadow-sm">
    <div class="card-body">

        <h5 class="card-title mb-2">Upload Excel</h5>

        {{-- SAMPLE FORMAT INFO (ADD HERE) --}}
        <div class="alert alert-info py-2 mb-3">
            <strong>Excel Format:</strong>
            <span class="ms-2">
                student_name | student_email | student_mobile | gender | score
            </span>


            <a href="{{ asset('sample/offline_students_sample.xlsx') }}"
   class="btn btn-sm btn-outline-primary ms-3"
   download>
    ⬇ Download Sample File
</a>

        </div>

        {{-- UPLOAD FORM --}}
        <form method="POST"
              enctype="multipart/form-data"
              action="{{ route('admin.offline.tests.upload', $test->id) }}"
              class="d-flex gap-2">
            @csrf
            <input type="file" name="file" class="form-control" required>
            <button class="btn btn-primary">Upload</button>
        </form>

    </div>
</div>


    {{-- ================= STUDENTS TABLE ================= --}}
    <form method="POST" action="{{ route('admin.offline.tests.finalize') }}">
        @csrf

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0">Students</h5>
                    <button class="btn btn-success btn-sm"
                            onclick="return confirm('Finalize selected students?')">
                        ✔ Finalize Selected
                    </button>
                </div>

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th width="100">Score</th>
                            <th width="140">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($studentTests as $st)
                            <tr>
                                <td>
                                    @if(!$st->is_finalized)
                                        <input type="checkbox"
                                               name="student_ids[]"
                                               value="{{ $st->id }}"
                                               class="student-checkbox">
                                    @endif
                                </td>

                                <td>{{ $st->student_name }}</td>
                                <td>{{ $st->student_email ?? '-' }}</td>

                                <td>
                                    <strong>{{ $st->score }}</strong>
                                </td>

                                 <td>
    @if(in_array($st->id, $movedStudentTestIds))
        <span class="badge bg-info">
            Moved to Enquiries
        </span>
    @elseif($st->is_finalized)
        <span class="badge bg-success">
            Finalized
        </span>
    @else
        <span class="badge bg-secondary">
            Pending
        </span>
    @endif
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No students found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </form>

    {{-- ================= MOVE TO ENQUIRIES ================= --}}
    <form method="POST"
          action="{{ route('admin.offline.tests.moveToenquiries', $test->id) }}"
          class="mt-3 text-end"
          onsubmit="return confirm('Move finalized students to enquiries?')">
        @csrf
        <button class="btn btn-warning">
            ➜ Move to Enquiries
        </button>
    </form>

</div>

{{-- ================= SELECT ALL SCRIPT ================= --}}
<script>
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.student-checkbox')
        .forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
