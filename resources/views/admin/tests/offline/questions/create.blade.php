<h3>Add Question</h3>

<form method="POST"
action="{{ route('admin.offline-questions.store',$test->id) }}">

@csrf

<div class="mb-3">
<label>Question</label>
<textarea name="question" class="form-control" rows="4"></textarea>
</div>

<div class="mb-3">
<label>Marks</label>
<input type="number" name="marks" class="form-control">
</div>

<button class="btn btn-success">Save</button>

</form>