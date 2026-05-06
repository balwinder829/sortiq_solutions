@extends('layouts.app')

@section('content')

<div class="container">

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a href="#" data-tab="states"
               class="nav-link tab-link active">
                States
            </a>
        </li>

        <li class="nav-item">
            <a href="#" data-tab="districts"
               class="nav-link tab-link">
                Districts
            </a>
        </li>
    </ul>

    {{-- Dynamic content --}}
    <div id="tab-content"></div>

</div>

@endsection


@push('scripts')
<script>
$(document).ready(function () {

    function loadTab(tab) {
        $('#tab-content').html(
            '<div class="text-center p-3">' +
            '<div class="spinner-border text-primary"></div>' +
            '</div>'
        );

        $.get("{{ route('states.index') }}", { tab: tab, ajax: 1 }, function (response) {
            $('#tab-content').html(response);
        });
    }

    // Load default tab
    loadTab('states');

    // Tab click
    $(document).on('click', '.tab-link', function (e) {
        e.preventDefault();

        $('.tab-link').removeClass('active');
        $(this).addClass('active');

        let tab = $(this).data('tab');

        // Update URL without reload
        window.history.pushState({}, '', '?tab=' + tab);

        loadTab(tab);
    });

});
</script>
@endpush