@extends('layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4>
            SIM Details
        </h4>

        <div>

            <a href="{{ route('sims.edit', $sim) }}"
               class="btn btn-primary">

                Edit

            </a>

            <a href="{{ route('sims.index') }}"
               class="btn btn-secondary">

                Back

            </a>

        </div>

    </div>


    <div class="card">

        <div class="card-body">

            <div class="row">

                {{-- Mobile --}}
                <div class="col-md-6 mb-3">

                    <strong>
                        Mobile Number
                    </strong>

                    <div>
                        {{ $sim->mobile_number }}
                    </div>

                </div>


                {{-- Operator --}}
                <div class="col-md-6 mb-3">

                    <strong>
                        Operator
                    </strong>

                    <div>
                        {{ $sim->operator }}
                    </div>

                </div>


                {{-- Name --}}
                <div class="col-md-6 mb-3">

                    <strong>
                        Name
                    </strong>

                    <div>
                        {{ $sim->name ?: '-' }}
                    </div>

                </div>


                {{-- Status --}}
                <div class="col-md-6 mb-3">

                    <strong>
                        Status
                    </strong>

                    <div>

                        @if($sim->status === 'active')

                            <span class="badge bg-success">
                                Active
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                Deactive
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Issued Date --}}
                <div class="col-md-6 mb-3">

                    <strong>
                        Issued Date
                    </strong>

                    <div>
                        {{ $sim->issued_at
                            ? $sim->issued_at->format('d M Y')
                            : '-' }}
                    </div>

                </div>


                {{-- Created By --}}
                <div class="col-md-6 mb-3">

                    <strong>
                        Created By
                    </strong>

                    <div>
                        {{ optional($sim->creator)->name ?: '-' }}
                    </div>

                </div>


                {{-- Notes --}}
                <div class="col-12 mb-3">

                    <strong>
                        Notes
                    </strong>

                    <div>
                        {{ $sim->notes ?: '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection