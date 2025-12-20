<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="4">{{ old('description', $event->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Event Date</label>
    <input type="date" name="event_date" value="{{ old('event_date', $event->event_date ?? '') }}" class="form-control">
</div>

<div class="mb-4">
    <label class="form-label">Upload Photos & Videos</label>
    <input type="file" name="media[]" multiple class="form-control">
    <small class="text-muted">You can select multiple files. Allowed: Images & Videos.</small>
</div>

<div class="mb-3">
    <label class="form-label">Select Cover Image (Choose uploaded file name)</label>
    <input type="text" name="cover_image" class="form-control" placeholder="Enter selected image filename (example.jpg)">
    <small class="text-muted">Type the exact original name of the uploaded image.</small>
</div>
