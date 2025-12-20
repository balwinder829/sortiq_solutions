@if($students->isEmpty())
    <p class="text-muted mb-0">No finalized students found.</p>
@else
<table class="table table-bordered table-sm mb-0">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Score</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $index => $st)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $st->student_name }}</td>
            <td>{{ $st->student_email }}</td>
            <td>{{ $st->score }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
