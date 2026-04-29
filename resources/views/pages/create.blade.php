@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Page</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('pages.store') }}">
@csrf

<div class="row">

    <div class="form-group col-md-6">
        <label>Title *</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="form-group col-md-6">
        <label>Slug * <span style="color: red;">( Use word services- for Services pages and internship- for Internship Pages )</span></label>
        <input type="text" name="slug" class="form-control" placeholder="Use services- for Services pages and internship- for Internship Pages" required>
    </div>

    <div class="form-group col-md-6">
        <label>Heading *</label>
        <input type="text" name="heading" class="form-control" required>
    </div>

    <div class="form-group col-md-6">
        <label>Location *</label>
        <input type="text" name="location" class="form-control" required>
    </div>

    <div class="form-group col-md-6">
        <label>Ads Type *</label>
        <select name="ads_type" class="form-control" required>
            <option value="">-- Select Type --</option>
            <option value="internship">Internship</option>
            <option value="services">Services</option>
            <option value="products">Products</option>
            <option value="single product">Single Product</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Status</label>
        <select name="is_active" class="form-control">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>

    <div class="form-group col-md-12">
        <label>Content</label>
        <textarea name="content" id="content" class="form-control"></textarea>
    </div>

    <!-- <div class="form-group col-md-6">
        <label>Meta Title</label>
        <input type="text" name="meta_title" class="form-control">
    </div>

    <div class="form-group col-md-6">
        <label>Meta Keywords</label>
        <input type="text" name="meta_keywords" class="form-control">
    </div>

    <div class="form-group col-md-12">
        <label>Meta Description</label>
        <textarea name="meta_description" class="form-control"></textarea>
    </div> -->

   <!--  <div class="form-group col-md-6">
        <label>Banner Image Path</label>
        <input type="text" name="banner_image" class="form-control">
    </div>

    <div class="form-group col-md-6">
        <label>Featured Image Path</label>
        <input type="text" name="featured_image" class="form-control">
    </div> -->

    

</div>

<button class="btn btn-primary mt-3">Save</button>
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
