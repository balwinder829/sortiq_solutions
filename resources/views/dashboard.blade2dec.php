@extends('layouts.app')

@section('content')
@php
    $role = (int) auth()->user()->role;
@endphp
<div class="container mt-4">
    @if($pendingStudents->count() > 0)
<div id="pending-fee-alert"
     class="position-fixed top-0 end-0 m-4 animate__animated animate__fadeInRight"
     style="z-index: 2000; width: 360px;">
  <div class="card border-danger shadow-lg">
    
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <i class="bx bx-money-withdraw me-2"></i>
        Pending Fee Alert
      </h5>
      <button type="button" class="btn-close btn-close-white" onclick="dismissPendingFee()"></button>
    </div>

    <div class="card-body bg-light text-dark">
      <p class="mb-2">
        <strong>{{ $pendingStudents->count() }}</strong> student(s) have pending fees.
      </p>

     <!--  <ul class="ps-3 mb-3" style="max-height: 180px; overflow-y: auto;">
        @foreach($pendingStudents as $s)
          <li>
            {{ $s->student_name }} — ₹{{ $s->pending_fees }}
            <br>
            <small class="text-muted">Due: {{ $s->next_due_date }}</small>
          </li>
        @endforeach
      </ul> -->

      <div class="d-flex justify-content-end gap-2">
        <button class="btn btn-sm btn-secondary" onclick="dismissPendingFee()">Dismiss</button>
        <a href="{{ url('/admin/pending-fees') }}" class="btn btn-sm btn-danger text-white">View</a>
      </div>

    </div>
  </div>
</div>

<script>
function dismissPendingFee() {
  fetch('{{ route('admin.pending_fees.dismiss') }}', {
      method: 'POST',
      headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
      }
  }).then(() => {
      const alertBox = document.getElementById('pending-fee-alert');
      alertBox.classList.add('animate__fadeOutRight');
      setTimeout(() => alertBox.remove(), 300);
  });
}
</script>
@endif


    <h2 class="mb-4">Dashboard</h2>

    {{-- Top summary boxes --}}
<div class="row g-3 mb-4">
    <!-- Row 1 -->
    @if(in_array($role, [1,2,3]))
    <div class="col-12 col-md-4">
         <a href="{{ route('students.index') }}" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Students</h6>
                    <h3 class="fw-bold">{{ $totalStudents }}</h3>
                </div>
            </div>
        </a>
    </div>
    @endif

    @if(in_array($role, [1,2]))
    <div class="col-12 col-md-4">
        <a href="{{ route('batches.index') }}" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Batches</h6>
                    <h3 class="fw-bold">{{ $totalBatches }}</h3>
                </div>
            </div>
        </a>
    </div>
    @endif

    @if(in_array($role, [1]))
    <div class="col-12 col-md-4">
        <a href="{{ route('colleges.index') }}" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Colleges</h6>
                    <h3 class="fw-bold">{{ $totalColleges }}</h3>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Row 2 -->
    @if(in_array($role, [1,2]))
    <div class="col-12 col-md-4">
        <a href="{{ route('trainers.index') }}" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Trainers</h6>
                    <h3 class="fw-bold">{{ $totalTrainers }}</h3>
                </div>
            </div>
        </a>
    </div>
    @endif

    @if(in_array($role, [1]))
    <div class="col-12 col-md-4">
        <a href="{{ route('sessions.index') }}" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Sessions</h6>
                    <h3 class="fw-bold">{{ $totalSessions }}</h3>
                </div>
            </div>
        </a>
    </div>
    @endif

    @if(in_array($role, [1]))
    <div class="col-12 col-md-4">
        <a href="{{ route('courses.index') }}" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Courses</h6>
                    <h3 class="fw-bold">{{ $totalCourses }}</h3>
                </div>
            </div>
        </a>
    </div>
    @endif

    <!-- Row 3 - Centered last card -->
    @if(in_array($role, [1]))
    <div class="col-12 col-md-4 offset-md-4">
        <a href="{{ route('admin.pendingfees.list') }}" class="text-decoration-none text-dark">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Pending Fee Students</h6>
                    <h3 class="fw-bold">{{ $pendingFeeStudents }}</h3>
                </div>
            </div>
        </a>
    </div>
    @endif
</div>
 
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let ctx = document.getElementById('sessionChart').getContext('2d');

// Initialize the chart
let sessionChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [], // session names
        datasets: [{
            label: 'Student Count',
            data: [], // student counts
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(context) {
                        return `Students: ${context.parsed.y}`;
                    }
                }
            }
        },
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Number of Students' } },
            x: { title: { display: true, text: 'Session' } }
        }
    }
});

// Update chart when a session is selected
document.getElementById('sessionSelect').addEventListener('change', function () {
    let sessionId = this.value;
    if (!sessionId) return;

    fetch(`/dashboard/session/${sessionId}/students`)
        .then(res => res.json())
        .then(data => {
            // Reset chart to show only selected session
            sessionChart.data.labels = [data.sessionName];
            sessionChart.data.datasets[0].data = [data.studentsCount];
            sessionChart.update();
        })
        .catch(err => console.error('Error fetching session data:', err));
});


</script>
@endpush
