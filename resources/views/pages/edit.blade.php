@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Page</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('pages.update', $page) }}">
@csrf
@method('PUT')

<div class="row">

    <div class="form-group col-md-6">
        <label>Title *</label>
        <input type="text"
               name="title"
               class="form-control"
               value="{{ old('title', $page->title) }}"
               required>
    </div>

    <div class="form-group col-md-6">
        <label>Slug *<span style="color: red;">( Use word services- for Services pages and internship- for Internship Pages )</span></label>
        <input type="text"
               name="slug"
               class="form-control"
               value="{{ old('slug', $page->slug) }}"
               required>
    </div>

    <div class="form-group col-md-6">
        <label>Heading *</label>
        <input type="text"
               name="heading"
               class="form-control"
               value="{{ old('heading', $page->heading) }}"
               required>
    </div>

    <div class="form-group col-md-6">
        <label>Location *</label>
        <input type="text"
               name="location"
               class="form-control"
               value="{{ old('location', $page->location) }}"
               required>
    </div>

    

   

    {{-- SEO --}}
   <!--  <div class="form-group col-md-6">
        <label>Meta Title</label>
        <input type="text"
               name="meta_title"
               class="form-control"
               value="{{ old('meta_title', $page->meta_title) }}">
    </div>

    <div class="form-group col-md-6">
        <label>Meta Keywords</label>
        <input type="text"
               name="meta_keywords"
               class="form-control"
               value="{{ old('meta_keywords', $page->meta_keywords) }}">
    </div>

    <div class="form-group col-md-12">
        <label>Meta Description</label>
        <textarea name="meta_description"
                  class="form-control">{{ old('meta_description', $page->meta_description) }}</textarea>
    </div>

    {{-- Images --}}
    <div class="form-group col-md-6">
        <label>Banner Image Path</label>
        <input type="text"
               name="banner_image"
               class="form-control"
               value="{{ old('banner_image', $page->banner_image) }}">
    </div>

    <div class="form-group col-md-6">
        <label>Featured Image Path</label>
        <input type="text"
               name="featured_image"
               class="form-control"
               value="{{ old('featured_image', $page->featured_image) }}">
    </div> -->

    <div class="form-group col-md-6">
        <label>Active Status</label>
        <select name="is_active" class="form-control">
            <option value="1" {{ $page->is_active ? 'selected' : '' }}>Active</option>
            <option value="0" {{ !$page->is_active ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Ads Running Status</label>
        <select name="ads_status" class="form-control">
            <option value="1" {{ $page->ads_status ? 'selected' : '' }}>Running</option>
            <option value="0" {{ !$page->ads_status ? 'selected' : '' }}>Not Running</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Ads Type *</label>
        <select name="ads_type" class="form-control" required>
            <option value="">-- Select Type --</option>

            <option value="internship" 
                {{ old('ads_type', $page->ads_type) == 'internship' ? 'selected' : '' }}>
                Internship
            </option>

            <option value="services" 
                {{ old('ads_type', $page->ads_type) == 'services' ? 'selected' : '' }}>
                Services
            </option>

            <option value="products" 
                {{ old('ads_type', $page->ads_type) == 'products' ? 'selected' : '' }}>
                Products
            </option>

            <option value="single product" 
                {{ old('ads_type', $page->ads_type) == 'single product' ? 'selected' : '' }}>
                Single Product
            </option>

        </select>
    </div>

     <div class="form-group col-md-12">
        <label>Content</label>
        <textarea name="content"
                  id="content"
                  class="form-control">{{ old('content', $page->content) }}</textarea>
    </div>

</div>

<button class="btn btn-primary mt-3">Update</button>
<a href="{{ route('pages.index') }}" class="btn btn-secondary mt-3">Back</a>

</form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('content');
</script>
@endpush
