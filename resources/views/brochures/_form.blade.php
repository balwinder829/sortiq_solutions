@php
    $editing = isset($brochure);
@endphp

<div class="mb-3">
    <label>Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $editing ? $brochure->title : '') }}" required>
</div>

<div class="mb-3">
    <label>Description</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $editing ? $brochure->description : '') }}</textarea>
</div>

<div class="mb-3">
    <label>File (PDF or Image)</label>
    <input type="file" name="file" class="form-control" {{ $editing ? '' : 'required' }}>
    @if($editing && $brochure->file_path)
        <small class="text-muted d-block mt-1">Current: {{ basename($brochure->file_path) }}</small>
    @endif
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $editing ? $brochure->is_active : true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Start At</label>
        <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at', $editing && $brochure->start_at ? $brochure->start_at->format('Y-m-d\TH:i') : '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label>End At</label>
        <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at', $editing && $brochure->end_at ? $brochure->end_at->format('Y-m-d\TH:i') : '') }}">
    </div>
</div>
