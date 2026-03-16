<h3>{{ $test->title }} - Questions</h3>

<a href="{{ route('admin.offline-questions.create',$test->id) }}"
class="btn btn-primary">Add Question</a>

<table class="table mt-3">
<thead>
<tr>
<th>#</th>
<th>Question</th>
<th>Marks</th>
<th>Action</th>
</tr>
</thead>

<tbody>
@foreach($questions as $q)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{!! $q->question !!}</td>
<td>{{ $q->marks }}</td>

<td>
<a href="{{ route('admin.offline-questions.edit',$q->id) }}"
class="btn btn-sm btn-warning">Edit</a>

<form method="POST"
action="{{ route('admin.offline-questions.destroy',$q->id) }}"
style="display:inline">
@csrf
@method('DELETE')

<button class="btn btn-sm btn-danger">Delete</button>
</form>

</td>

</tr>
@endforeach
</tbody>
</table>