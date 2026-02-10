@extends('layouts.app')

@section('content')
<div class="container">

<h1 class="page_heading">
    Payroll – {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
</h1>

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif


<form method="POST" action="{{ route('admin.payroll.store') }}">
@csrf

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>Emp Code</th>
    <th>Name</th>
    <th>Gross Salary</th>
    <th>Allowed Leave</th>
    <th>Taken Leave</th>
    <th>Final Salary</th>
</tr>
</thead>

<tbody>
@foreach($payrolls as $i => $p)
@php
    $hasSalary = $p->gross_salary > 0;
@endphp

<tr data-gross="{{ $p->gross_salary }}"
    class="{{ !$hasSalary ? 'table-warning' : '' }}">

    <td>{{ $p->employee->emp_code }}</td>
    <td>{{ $p->employee->emp_name }}</td>
    <td>{{ number_format($p->gross_salary,2) }}</td>

    <td>
        <input type="hidden" name="payroll_ids[]" value="{{ $p->id }}">
        <input type="hidden" name="gross_salary[{{ $p->id }}]" value="{{ $p->gross_salary }}">

        <input type="number"
               name="allowed_leave[{{ $p->id }}]"
               class="allowed form-control"
               value="{{ $p->allowed_leave }}"
               {{ !$hasSalary ? 'disabled' : '' }}>
    </td>

    <td>
        <input type="number"
               name="taken_leave[{{ $p->id }}]"
               class="taken form-control"
               value="{{ $p->taken_leave }}"
               {{ !$hasSalary ? 'disabled' : '' }}>
    </td>

    <td>
        <span class="final-salary">
            {{ number_format($p->final_salary,2) }}
        </span>
    </td>
</tr>

@endforeach
</tbody>
</table>

<button class="btn"
        style="background-color:#6b51df;color:#fff;">
    Save Payroll
</button>

</form>
</div>
@endsection

@push('scripts')
<script>
function calculate(row) {
    let gross = parseFloat(row.data('gross')) || 0;
    let allowed = parseFloat(row.find('.allowed').val()) || 0;
    let taken = parseFloat(row.find('.taken').val()) || 0;

    let perDay = gross / 30;
    let extra = Math.max(0, taken - allowed);
    let finalSalary = gross - (extra * perDay);

    row.find('.final-salary').text(finalSalary.toFixed(2));
}

$('.allowed, .taken').on('input', function () {
    calculate($(this).closest('tr'));
});

// Initial calculation
$('tr[data-gross]').each(function () {
    calculate($(this));
});
</script>
@endpush
