@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3>Preview Student Import</h3>

    <form action="{{ route('students.importSave') }}" method="POST">
        @csrf
        <input type="hidden" name="file" value="{{ $file }}">

        <table class="table table-bordered">
            <thead>
                <tr>
                    @foreach($headers as $head)
                        <th>{{ ucfirst($head) }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($rows as $index => $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-success">Confirm & Import</button>
        <a href="{{ route('students.importForm') }}" class="btn btn-secondary">Cancel</a>
    </form>

</div>
@endsection
