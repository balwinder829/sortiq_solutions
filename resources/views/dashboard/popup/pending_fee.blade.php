<div class="card border-danger shadow-lg">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bx bx-money-withdraw me-2"></i>Pending Fee Alert</h5>
        <button type="button" class="btn-close btn-close-white" onclick="dismissPendingFee()"></button>
    </div>

    <div class="card-body bg-light text-dark">
        <p class="mb-2">
            <strong>{{ $pendingStudents->count() }}</strong> student(s) have pending fees.
        </p>

        <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-sm btn-secondary" onclick="dismissPendingFee()">Dismiss</button>
            <a href="{{ url('/admin/pending-fees') }}" class="btn btn-sm btn-danger text-white">View</a>
        </div>
    </div>
</div>

<script>
function dismissPendingFee() {
  fetch('{{ route('admin.pending_fees.dismiss') }}', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
  }).then(() => {
      let alertBox = document.getElementById('pending-fee-alert');
      alertBox.classList.add('animate__fadeOutRight');
      setTimeout(() => alertBox.remove(), 300);
  });
}
</script>
