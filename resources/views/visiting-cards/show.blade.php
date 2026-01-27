@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-2">
        <div class="col-md-10">
            <h1 class="page_heading">Visiting Card Details</h1>
        </div>
        <div class="col-md-2">
            <div class="d-flex justify-content-end">
                <a href="{{ route('visiting-cards.index') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            <table class="table table-borderless">
                <tr>
                    <th width="30%">Name</th>
                    <td>{{ $visiting_card->name }}</td>
                </tr>
                <tr>
                    <th>Designation</th>
                    <td>{{ $visiting_card->designation }}</td>
                </tr>
                <tr>
                    <th>Company</th>
                    <td>{{ $visiting_card->company_name }}</td>
                </tr>
                <tr>
                    <th>Primary Phone</th>
                    <td>{{ $visiting_card->phone_primary }}</td>
                </tr>
                <tr>
                    <th>Secondary Phone</th>
                    <td>{{ $visiting_card->phone_secondary ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $visiting_card->email ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $visiting_card->address ?? '-' }}</td>
                </tr>
            </table>

            <hr>

            <h6 class="mb-3">Visiting Card Images</h6>

            <div class="row">

                {{-- FRONT IMAGE --}}
                <div class="col-md-6">
                    <label class="fw-semibold mb-2">Front Side</label><br>
                    @if($visiting_card->card_front && file_exists(public_path($visiting_card->card_front)))
                        <img src="{{ asset($visiting_card->card_front) }}"
                             class="img-fluid rounded border"
                             style="max-height:250px;">
                    @else
                        <p class="text-muted">No front image uploaded</p>
                    @endif
                </div>

                {{-- BACK IMAGE --}}
                <div class="col-md-6">
                    <label class="fw-semibold mb-2">Back Side</label><br>
                    @if($visiting_card->card_back && file_exists(public_path($visiting_card->card_back)))
                        <img src="{{ asset($visiting_card->card_back) }}"
                             class="img-fluid rounded border"
                             style="max-height:250px;">
                    @else
                        <p class="text-muted">No back image uploaded</p>
                    @endif
                </div>

            </div>

        </div>
    </div>

</div>
@endsection
