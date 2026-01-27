@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Generate Salary Slips</h4>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('salary-slips.generate') }}">
        @csrf

        <div class="row">

            <div class="form-group col-md-4">
                <label>Month</label>
                <select name="month" class="form-control" required>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>

            <div class="form-group col-md-4">
                <label>Year</label>
                <select name="year" class="form-control" required>
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

        </div>

        <button class="btn btn-primary mt-3">Generate</button>
        <a href="{{ route('salary-slips.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
