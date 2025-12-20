@extends('layouts.app')

@section('content')
<div class="container mt-5 text-center">
    <h3>Check Your Trainings</h3>
    <form id="trainingCheckForm" class="mt-4">
        @csrf
        <input type="text" name="student_id" id="student_id" placeholder="Enter your Student ID" class="form-control mb-2" style="max-width:300px; margin:auto;">
        <button type="submit" class="btn btn-primary">Check</button>
    </form>

    <div id="trainingResult" class="mt-4"></div>

    <table id="trainingDataTable" class="table table-bordered mt-3" style="display:none; margin:auto;">
        <thead>
            <tr>
                <th>Training Name</th>
                <th>Status</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){

    $('#trainingCheckForm').submit(function(e){
        e.preventDefault();

        var student_id = $('#student_id').val();

        $.ajax({
            url: '{{ route("training.check") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                student_id: student_id
            },
            success: function(response){
                if(response.exists){
                    let rows = '';
                    response.data.forEach(function(training){
                        rows += `<tr>
                                    <td>${training.name}</td>
                                    <td>${training.status}</td>
                                    <td>${training.start_date}</td>
                                    <td>${training.end_date}</td>
                                </tr>`;
                    });
                    $('#trainingDataTable tbody').html(rows).show();
                    $('#trainingResult').text('');
                } else {
                    $('#trainingDataTable').hide();
                    $('#trainingResult').text(response.message).css('color','red');
                }
            }
        });
    });

});
</script>
@endsection
