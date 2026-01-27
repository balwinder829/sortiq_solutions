<?php

namespace App\Http\Controllers;

use App\Models\Scanner;

class ScannerShareController extends Controller
{
    /**
     * Show ALL public scanners (3 per row)
     * URL: /scanners
     */
    public function index()
    {
        $scanners = Scanner::where('is_public', true)
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('frontend.scanners.index', compact('scanners'));
    }

    /**
     * Show SINGLE scanner by share token
     * URL: /scanners/view/{token}
     */
    public function show(string $token)
    {
        $scanner = Scanner::where('share_token', $token)
            ->where('is_public', true)
            ->where('is_active', true)
            ->firstOrFail();

        // increment views
        $scanner->increment('view_count');

        return view('frontend.scanners.show', compact('scanner'));
    }
}
