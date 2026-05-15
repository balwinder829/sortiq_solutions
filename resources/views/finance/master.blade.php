@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">

        <li class="nav-item">

            <a href="#"
               class="nav-link finance-tab active"
               data-tab="electricity">

                Electricity Bills

            </a>

        </li>
        <li class="nav-item">

            <a href="#"
               class="nav-link finance-tab"
               data-tab="pantry">

                Pantry Expenses

            </a>

        </li>

        <li class="nav-item">

            <a href="#"
               class="nav-link finance-tab"
               data-tab="office-paper">

                Office Paper Expenses

            </a>

        </li>

        <li class="nav-item">

            <a href="#"
               class="nav-link finance-tab"
               data-tab="tea-pantry">

                Tea Pantry Expenses

            </a>

        </li>
        <li class="nav-item">

            <a href="#"
               class="nav-link finance-tab"
               data-tab="office-cleaning">

                Office Cleaning Expenses

            </a>

        </li>
        <li class="nav-item">

            <a href="#"
               class="nav-link finance-tab"
               data-tab="office-accessories">

                Office Accessories Expenses

            </a>

        </li>
        <li class="nav-item">

            <a href="#"
               class="nav-link finance-tab"
               data-tab="event-expenses">

                Event Expenses

            </a>

        </li>

    </ul>

    {{-- Dynamic Content --}}
    <div id="finance-content"></div>

</div>

@endsection


@push('scripts')

<script>

$(document).ready(function () {

    function loadFinanceTab(tab) {

        // Set active tab UI
        $('.finance-tab').removeClass('active');

        $('.finance-tab[data-tab="' + tab + '"]')
            .addClass('active');

        // Update URL
        window.history.replaceState(
            {},
            '',
            '?tab=' + tab
        );

        // Loader
        $('#finance-content').html(

            '<div class="text-center p-5">' +

                '<div class="spinner-border text-primary"></div>' +

            '</div>'
        );

        $.get(

            "{{ route('finance.index') }}",

            {
                tab: tab,
                ajax: 1
            },

            function (response) {

                $('#finance-content').html(response);

            }
        );
    }

    // Read tab from URL
    let activeTab =
        new URLSearchParams(window.location.search)
            .get('tab')
        || 'electricity';

    // Initial load
    loadFinanceTab(activeTab);

    // Tab click
    $(document).on('click', '.finance-tab', function (e) {

        e.preventDefault();

        let tab = $(this).data('tab');

        loadFinanceTab(tab);

    });

});

</script>

@endpush