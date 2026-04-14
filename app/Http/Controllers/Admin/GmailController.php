<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GmailController extends Controller
{
    public function __construct(protected GmailService $gmail)
    {
    }

    public function index(Request $request, string $account = 'sortiq')
    {
        set_time_limit(90);

        $folder = $request->query('folder', 'inbox');
        $filter = $request->query('filter');
        $q = $request->query('q');
        $subject = $request->query('subject');
        $limitParam = $request->query('limit');
        $listLimit = null;
        if ($limitParam !== null && $limitParam !== '') {
            $max = (int) config('gmail.imap_list_limit_max', 200);
            $listLimit = max(5, min((int) $limitParam, $max));
        }

        $accounts = config('gmail.accounts');
        abort_unless(isset($accounts[$account]), 404);

        $imapError = null;
        try {
            $messages = $this->gmail->listMessages($account, $folder, $filter, $q, $subject, $listLimit);
        } catch (\Throwable $e) {
            report($e);
            $messages = [];
            $imapError = $e->getMessage();
        }

        return view('admin.gmail.index', [
            'messages' => $messages,
            'accounts' => $accounts,
            'currentAccount' => $account,
            'folder' => $folder,
            'filter' => $filter,
            'q' => $q,
            'subject' => $subject,
            'perPage' => $listLimit ?? (int) config('gmail.imap_list_limit', 50),
            'imapError' => $imapError,
        ]);
    }

    public function replyForm(Request $request, string $account, int $uid)
    {
        $folder = $request->query('folder', 'inbox');

        $accounts = config('gmail.accounts');
        abort_unless(isset($accounts[$account]), 404);

        $message = $this->gmail->getMessage($account, $folder, $uid);

        abort_unless($message, 404);

        $from = $message->getReplyTo()[0] ?? $message->getFrom()[0] ?? null;

        $replySubject = $message->getSubject();
        if (is_object($replySubject) && method_exists($replySubject, '__toString')) {
            $replySubject = (string) $replySubject;
        }

        $listFilters = array_filter([
            'folder' => $folder,
            'filter' => $request->query('filter'),
            'q' => $request->query('q'),
            'subject' => $request->query('subject'),
            'limit' => $request->query('limit'),
        ], static fn ($v) => $v !== null && $v !== '');

        return view('admin.gmail.reply', [
            'account' => $account,
            'uid' => $uid,
            'folder' => $folder,
            'to_email' => $from ? $from->mail : null,
            'to_name' => $from ? $from->personal : null,
            'subject' => 'Re: '.$replySubject,
            'original_snippet' => Str::limit(strip_tags($message->getTextBody() ?? $message->getHTMLBody()), 300),
            'listFilters' => $listFilters,
        ]);
    }

    public function sendReply(Request $request, string $account, int $uid)
    {
        $accounts = config('gmail.accounts');
        abort_unless(isset($accounts[$account]), 404);
        $config = $accounts[$account];

        $data = $request->validate([
            'to' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $mailer = $config['mailer'] ?? config('mail.default');

        Mail::mailer($mailer)->raw($data['body'], function ($message) use ($data, $config) {
            $message->to($data['to'])
                ->subject($data['subject'])
                ->from($config['email'], $config['label'] . ' - Sortiq Solutions');
        });

        $back = array_filter([
            'account' => $account,
            'folder' => $request->input('return_folder', 'inbox'),
            'filter' => $request->input('return_filter'),
            'q' => $request->input('return_q'),
            'subject' => $request->input('return_subject_contains'),
            'limit' => $request->input('return_limit'),
        ], static fn ($v) => $v !== null && $v !== '');

        return redirect()
            ->route('admin.gmail.index', $back)
            ->with('success', 'Reply sent successfully.');
    }
}

