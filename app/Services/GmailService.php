<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;

class GmailService
{
    protected ClientManager $manager;

    public function __construct()
    {
        $this->manager = new ClientManager();
    }

    public function listMessages(
        string $accountKey,
        string $folder = 'inbox',
        ?string $filter = null,
        ?string $query = null,
        ?string $subject = null,
        ?int $limit = null,
    ): array {
        $config = config("gmail.accounts.$accountKey");
        if (!$config || empty($config['imap_password'])) {
            return [];
        }

        $maxCap = (int) config('gmail.imap_list_limit_max', 150);
        $defaultLimit = (int) config('gmail.imap_list_limit', 50);
        $limit = $limit ?? $defaultLimit;
        $limit = max(5, min($limit, $maxCap));
        $sinceDays = (int) config('gmail.imap_list_since_days', 30);

        $client = $this->makeClient($config);
        $imapTimeout = isset($config['imap_timeout']) && $config['imap_timeout'] !== null && $config['imap_timeout'] !== ''
            ? (int) $config['imap_timeout']
            : (int) config('gmail.imap_connection_timeout', 20);
        $imapTimeout = max(5, min($imapTimeout, 60));
        $prevSocketTimeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', (string) max(15, $imapTimeout + 15));

        try {
            $client->connect();

            $folderName = $folder === 'sent' ? '[Gmail]/Sent Mail' : 'INBOX';
            $mailbox = $client->getFolder($folderName);

            if ($mailbox === null) {
                return [];
            }

            // Headers only: fetching full bodies for every row is very slow and can hit PHP time limits.
            $queryBuilder = $mailbox->messages()
                ->since(now()->subDays($sinceDays))
                ->fetchBody(false)
                ->leaveUnread()
                ->fetchOrderDesc();

            $subjectTrimmed = $subject !== null ? trim($subject) : '';
            if ($subjectTrimmed !== '') {
                $queryBuilder->subject($subjectTrimmed);
            }

            if ($query) {
                $queryBuilder->text($query);
            }

            if ($accountKey === 'hr' && $folder === 'inbox') {
                if ($filter === 'leaves') {
                    $queryBuilder->text('leave');
                } elseif ($filter === 'college') {
                    $queryBuilder->text('college');
                }
            }

            if ($accountKey === 'sortiq' && $folder === 'inbox' && $filter === 'queries') {
                // Avoid literal "OR" in TEXT (non-standard / can hang on some servers). Match common keywords.
                $queryBuilder->text('enquiry')->orWhere(function ($q) {
                    $q->text('query');
                });
            }

            $messages = $queryBuilder->limit($limit)->get();

            $sorted = $messages->sortByDesc(function ($msg) {
                $d = $msg->getDate();
                if (is_object($d) && method_exists($d, 'toDate')) {
                    $d = $d->toDate();
                }
                if ($d instanceof Carbon) {
                    return $d->getTimestamp();
                }

                return 0;
            })->values();

            return $sorted->map(function ($msg) use ($accountKey, $folder) {
                $fromAddr = $msg->getFrom()[0] ?? null;

                $subject = $msg->getSubject();
                if (is_object($subject) && method_exists($subject, '__toString')) {
                    $subject = (string) $subject;
                }
                $subject = self::decodeMimeHeader($subject);

                $date = $msg->getDate();
                if (is_object($date) && method_exists($date, 'toDate')) {
                    $date = $date->toDate();
                }

                $fromName = $fromAddr ? self::decodeMimeHeader((string) ($fromAddr->personal ?? '')) : null;

                return [
                    'uid' => $msg->getUid(),
                    'message_id' => $msg->getMessageId(),
                    'subject' => $subject,
                    'from_email' => $fromAddr ? $fromAddr->mail : null,
                    'from_name' => $fromName ?: null,
                    'date' => $date,
                    'seen' => $msg->getFlags()->contains('\\Seen'),
                    'account' => $accountKey,
                    'folder' => $folder,
                    'snippet' => Str::limit($subject, 120),
                ];
            })->values()->all();
        } finally {
            if ($prevSocketTimeout !== false) {
                ini_set('default_socket_timeout', $prevSocketTimeout);
            }
            try {
                $client->disconnect();
            } catch (\Throwable) {
                // ignore
            }
        }
    }

    public function getMessage(string $accountKey, string $folder, int $uid)
    {
        $config = config("gmail.accounts.$accountKey");
        if (!$config || empty($config['imap_password'])) {
            return null;
        }

        $client = $this->makeClient($config);
        $client->connect();

        $folderName = $folder === 'sent' ? '[Gmail]/Sent Mail' : 'INBOX';
        $mailbox = $client->getFolder($folderName);

        if ($mailbox === null) {
            try {
                $client->disconnect();
            } catch (\Throwable) {
            }

            return null;
        }

        // Keep connection open: Message uses Client for lazy-loaded body in the reply view.
        return $mailbox->query()->getMessageByUid($uid);
    }

    protected function makeClient(array $config): Client
    {
        $timeout = isset($config['imap_timeout']) && $config['imap_timeout'] !== null && $config['imap_timeout'] !== ''
            ? (int) $config['imap_timeout']
            : (int) config('gmail.imap_connection_timeout', 20);

        return $this->manager->make([
            'host' => $config['imap_host'],
            'port' => $config['imap_port'],
            'encryption' => $config['imap_encryption'],
            'validate_cert' => config('gmail.imap_validate_cert', true),
            'username' => $config['imap_username'],
            'password' => $config['imap_password'],
            'protocol' => 'imap',
            'timeout' => max(5, min($timeout, 60)),
        ]);
    }

    /**
     * Decode RFC 2047 encoded-words in Subject / From name (e.g. =?utf-8?B?...?=).
     */
    protected static function decodeMimeHeader(string $value): string
    {
        $value = trim($value);
        if ($value === '' || ! str_contains($value, '=?')) {
            return $value;
        }

        if (function_exists('iconv_mime_decode')) {
            $decoded = @iconv_mime_decode($value, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
            if (is_string($decoded) && $decoded !== '') {
                return trim($decoded);
            }
        }

        if (function_exists('mb_decode_mimeheader')) {
            return trim(mb_decode_mimeheader($value));
        }

        return $value;
    }
}

