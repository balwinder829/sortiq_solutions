@extends('layouts.app')

@section('title', 'Gmail')

@section('content')
    @php
        $gmailKeep = ['folder' => $folder];
        if (!empty($filter)) {
            $gmailKeep['filter'] = $filter;
        }
        if (!empty($q)) {
            $gmailKeep['q'] = $q;
        }
        if (!empty($subject ?? null)) {
            $gmailKeep['subject'] = $subject;
        }
        $gmailKeep['limit'] = $perPage ?? (int) config('gmail.imap_list_limit', 50);
    @endphp

    @if(!empty($imapError))
        <div class="alert alert-danger">
            <strong>Gmail / IMAP error.</strong>
            {{ $imapError }}
            <hr class="my-2">
            <small class="text-muted">
                Check: password in <code>.env</code>, IMAP enabled in Gmail settings, firewall allows outbound port 993,
                and optionally <code>GMAIL_IMAP_CONNECTION_TIMEOUT</code> / <code>GMAIL_IMAP_VALIDATE_CERT=false</code> for local testing.
            </small>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <h4>Gmail – {{ $accounts[$currentAccount]['label'] ?? ucfirst($currentAccount) }}</h4>
        </div>
        <div class="col-md-6">
            <form method="get" class="row g-2 justify-content-end">
                <input type="hidden" name="folder" value="{{ $folder }}">
                @if($filter)
                    <input type="hidden" name="filter" value="{{ $filter }}">
                @endif
                <div class="col-12 col-md-5">
                    <input type="text" name="subject" class="form-control form-control-sm" placeholder="Filter by subject…" value="{{ $subject }}">
                </div>
                <div class="col-12 col-md-4">
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Search message text…" value="{{ $q }}">
                </div>
                <div class="col-6 col-md-2">
                    <select name="limit" class="form-select form-select-sm" title="Max emails to load (server)">
                        @foreach([25, 50, 100, 150, 200] as $n)
                            <option value="{{ $n }}" @selected((int)($perPage ?? 50) === $n)>{{ $n }} rows</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.gmail.index', array_merge(['account' => 'sortiq'], $gmailKeep)) }}"
                   class="btn btn-sm {{ $currentAccount === 'sortiq' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Queries (sortiqsolutions@gmail.com)
                </a>
                <a href="{{ route('admin.gmail.index', array_merge(['account' => 'hr'], $gmailKeep)) }}"
                   class="btn btn-sm {{ $currentAccount === 'hr' ? 'btn-primary' : 'btn-outline-primary' }}">
                    HR (hr.sortiqsolutions@gmail.com)
                </a>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.gmail.index', array_merge(['account' => $currentAccount, 'folder' => 'inbox'], array_diff_key($gmailKeep, ['folder' => true]))) }}"
                   class="btn btn-sm {{ $folder === 'inbox' ? 'btn-success' : 'btn-outline-success' }}">
                    Inbox
                </a>
                <a href="{{ route('admin.gmail.index', array_merge(['account' => $currentAccount, 'folder' => 'sent'], array_diff_key($gmailKeep, ['folder' => true]))) }}"
                   class="btn btn-sm {{ $folder === 'sent' ? 'btn-success' : 'btn-outline-success' }}">
                    Sent
                </a>
            </div>
        </div>
        <div class="col-md-4 text-end">
            @if($currentAccount === 'hr' && $folder === 'inbox')
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.gmail.index', array_merge(['account' => $currentAccount, 'folder' => 'inbox'], array_diff_key($gmailKeep, ['folder' => true, 'filter' => true]))) }}"
                       class="btn {{ !$filter ? 'btn-outline-secondary' : 'btn-outline-light border' }}">
                        All
                    </a>
                    <a href="{{ route('admin.gmail.index', array_merge(['account' => $currentAccount, 'folder' => 'inbox', 'filter' => 'leaves'], array_diff_key($gmailKeep, ['folder' => true, 'filter' => true]))) }}"
                       class="btn {{ $filter === 'leaves' ? 'btn-warning' : 'btn-outline-warning' }}">
                        Leaves
                    </a>
                    <a href="{{ route('admin.gmail.index', array_merge(['account' => $currentAccount, 'folder' => 'inbox', 'filter' => 'college'], array_diff_key($gmailKeep, ['folder' => true, 'filter' => true]))) }}"
                       class="btn {{ $filter === 'college' ? 'btn-info' : 'btn-outline-info' }}">
                        College
                    </a>
                </div>
            @elseif($currentAccount === 'sortiq' && $folder === 'inbox')
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.gmail.index', array_merge(['account' => $currentAccount, 'folder' => 'inbox'], array_diff_key($gmailKeep, ['folder' => true, 'filter' => true]))) }}"
                       class="btn {{ !$filter ? 'btn-outline-secondary' : 'btn-outline-light border' }}">
                        All
                    </a>
                    <a href="{{ route('admin.gmail.index', array_merge(['account' => $currentAccount, 'folder' => 'inbox', 'filter' => 'queries'], array_diff_key($gmailKeep, ['folder' => true, 'filter' => true]))) }}"
                       class="btn {{ $filter === 'queries' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Queries
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover" id="gmailTable">
                <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Snippet</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($messages as $m)
                    <tr class="{{ empty($m['seen']) ? 'fw-bold' : '' }}">
                        <td>
                            {{ $m['from_name'] ?: $m['from_email'] }}
                            <br><small class="text-muted">{{ $m['from_email'] }}</small>
                        </td>
                        <td>{{ $m['subject'] }}</td>
                        <td>{{ $m['snippet'] }}</td>
                        <td data-order="{{ $m['date'] instanceof \Carbon\Carbon ? $m['date']->timestamp : 0 }}">
                            {{ optional($m['date'])->format('d M Y H:i') }}
                        </td>
                        <td>
                            @if($folder === 'inbox')
                                <a href="{{ route('admin.gmail.reply.form', array_merge(['account' => $currentAccount, 'uid' => $m['uid']], $gmailKeep)) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Reply
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    {{-- One cell per column (no colspan) so DataTables column count stays correct --}}
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-center text-muted">No emails found. Check IMAP settings or try another filter.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#gmailTable').DataTable({
            pageLength: 25,
            order: [[3, 'desc']]
        });
    });
</script>
@endpush

