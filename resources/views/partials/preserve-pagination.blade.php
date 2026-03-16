{{-- Include in edit/update forms so redirect after save stays on same page --}}
@if(request('page'))
<input type="hidden" name="page" value="{{ request('page') }}">
@endif
