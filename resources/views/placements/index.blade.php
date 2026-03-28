@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }
</style>

<div class="container">

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Student's Placements</h1>
        </div>
         
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                 <a href="{{ route('placements.create') }}" class="btn mb-3" style="background-color:#6b51df;color:#fff;"> Add Placement</a>
            </div>
        </div>
    </div>   

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTERS --}}
    <form method="GET" id="filterForm" class="mb-3">
        <div class="row g-2">

            <div class="col-md-2">
                <select name="college_id" class="form-select filterchange select2">
                    <option value="">All Colleges</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}">{{ $college->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="state_id" class="form-select filterchange">
                    <option value="">All States</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <input type="text" name="location"
                       class="form-control filterchangetext"
                       placeholder="Location">
            </div>

            <div class="col-md-2">
                <select name="tech" class="form-select filterchange">
                    <option value="">All Technology</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="session_id" class="form-select filterchange">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}">{{ $session->session_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <input type="text" name="student_name"
                       class="form-control filterchangetext"
                       placeholder="Student">
            </div>

            <div class="col-md-2">
                <select name="company" class="form-select filterchange">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="media" class="form-select filterchange">
                    <option value="">All Media</option>
                    <option value="with">With Media</option>
                    <option value="without">No Media</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="missing" class="form-select filterchange">
                    <option value="">All Data</option>
                    <option value="yes">Missing Info</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="button" id="resetFilters"
                        class="btn btn-secondary w-100">
                    Reset
                </button>
            </div>

        </div>
    </form>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="placementTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>College</th>
                <th>State</th>
                <th>Location</th>
                <th>Tech</th>
                <th>Session</th>
                <th>Media</th>
                <th width="150">Actions</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

{{-- IMAGE MODAL --}}
<div class="modal fade" id="imageModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-body text-center">
        <img id="modalMainImage" 
             style="width:100%;max-height:400px;object-fit:contain;">
        
        <div id="modalThumbnails" class="mt-3 d-flex flex-wrap gap-2 justify-content-center"></div>
      </div>

    </div>
  </div>
</div>

@endsection

@push('scripts')

<script>
let table = $('#placementTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('placements.index') }}",
        data: function (d) {
            d.college_id   = $('[name=college_id]').val();
            d.state_id     = $('[name=state_id]').val();
            d.tech         = $('[name=tech]').val();
            // d.session_id   = $('[name=session_id]').val();
            d.session_id = $('[name=session_id]').find(":selected").val();
            d.location     = $('[name=location]').val();
            d.student_name = $('[name=student_name]').val();
            d.company      = $('[name=company]').val();
            d.media        = $('[name=media]').val();
            d.missing      = $('[name=missing]').val();
        }
    },
    columns: [
        { data: 'id' },
        { data: 'student' },
        { data: 'college' },
        { data: 'state' },
        { data: 'location' },
        { data: 'tech' },
        { data: 'session' },
        { data: 'media', orderable: false, searchable: false },
        { data: 'actions', orderable: false, searchable: false },
    ]
});
</script>

<script>
$(document).ready(function(){

    let timer;

    $('.filterchange').on('change', function(){
        table.ajax.reload();
    });

    $('.filterchangetext').on('input', function(){
        clearTimeout(timer);
        timer = setTimeout(function(){
            table.ajax.reload();
        }, 500);
    });

    // RESET WITHOUT RELOAD
    $('#resetFilters').on('click', function(){
        $('#filterForm').find('input, select').val('');
        $('#filterForm select').prop('selectedIndex', 0);
        table.ajax.reload();
    });

});
</script>

<script>
// IMAGE POPUP
$(document).on('click', '.placement-img', function(){

    let images = $(this).data('images') || [];
    let baseUrl = "{{ asset('') }}";

    if(images.length === 0) return;

    $('#modalMainImage').attr('src', baseUrl + images[0]);

    let thumbs = '';
    images.forEach(img => {
        thumbs += `<img src="${baseUrl + img}" 
                     class="thumb-img"
                     style="width:70px;height:70px;object-fit:cover;cursor:pointer;border-radius:6px;">`;
    });

    $('#modalThumbnails').html(thumbs);

    $('#imageModal').modal('show');
});

$(document).on('click', '.thumb-img', function(){
    $('#modalMainImage').attr('src', $(this).attr('src'));
});
</script>

@endpush