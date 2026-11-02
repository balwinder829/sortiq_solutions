
@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Test Linking</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.attendance-test-options.index') }}"
                   class="btn btn-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <form method="POST"
                  action="{{ route('admin.attendance-test-options.store') }}">
                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Attendance Test / Form</label>

                        <select name="external_attendance_test_id"
                                class="form-select"
                                required>
                            <option value="">Select Attendance Test</option>

                            @foreach ($attendanceTests as $attendanceTest)
                                <option value="{{ $attendanceTest->id }}"
                                    {{ old('external_attendance_test_id') == $attendanceTest->id ? 'selected' : '' }}>
                                    {{ $attendanceTest->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Test Type</label>

                        <select name="test_type"
                                id="test_type"
                                class="form-select"
                                required>
                            <option value="non_technical"
                                {{ old('test_type', 'non_technical') === 'non_technical' ? 'selected' : '' }}>
                                Non-Technical
                            </option>

                            <option value="technical"
                                {{ old('test_type') === 'technical' ? 'selected' : '' }}>
                                Technical
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Category</label>

                        <select name="category_id"
                                class="form-select"
                                required>
                            <option value="">Select Category</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Existing Online Test</label>

                        <select name="test_id"
                                class="form-select"
                                required>
                            <option value="">Select Online Test</option>

                            @foreach ($tests as $test)
                                <option value="{{ $test->id }}"
                                    {{ old('test_id') == $test->id ? 'selected' : '' }}>
                                    {{ $test->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Display Name</label>

                        <input type="text"
                               name="display_name"
                               class="form-control"
                               value="{{ old('display_name') }}"
                               placeholder="Example: HR Test"
                               required>
                    </div>

                    <div class="col-md-4 mb-3" id="default_option_wrapper">
                        <label class="fw-bold">Default Option</label>

                        <select name="is_default"
                                id="is_default"
                                class="form-select">
                            <option value="0"
                                {{ old('is_default', '0') == '0' ? 'selected' : '' }}>
                                No
                            </option>

                            <option value="1"
                                {{ old('is_default') == '1' ? 'selected' : '' }}>
                                Yes
                            </option>
                        </select>

                        <small class="text-muted">
                            One default option is allowed per category.
                        </small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Display Order</label>

                        <input type="number"
                               name="sort_order"
                               class="form-control"
                               min="0"
                               value="{{ old('sort_order', 0) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Status</label>

                        <select name="is_active"
                                class="form-select"
                                required>
                            <option value="1"
                                {{ old('is_active', '1') == '1' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ old('is_active') == '0' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.attendance-test-options.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn"
                            style="background-color:#6b51df;color:#fff;">
                        Save Option
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const testType = document.getElementById('test_type');
    const defaultWrapper = document.getElementById('default_option_wrapper');
    const defaultSelect = document.getElementById('is_default');

    function toggleDefaultOption() {
        const isTechnical = testType.value === 'technical';

        defaultWrapper.style.display = isTechnical ? 'none' : '';

        if (isTechnical) {
            defaultSelect.value = '0';
        }
    }

    testType.addEventListener('change', toggleDefaultOption);

    toggleDefaultOption();
});
</script>
@endsection