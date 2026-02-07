<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CurrentActorTrait
{
    protected function currentActor()
    {
        // Trainer logged in
        if (Auth::guard('trainer')->check()) {
            return [
                'type' => 'trainer',
                'id'   => Auth::guard('trainer')->id(),
            ];
        }

        // Employee / Admin logged in
        if (Auth::check()) {
            return [
                'type' => 'employee',
                'id'   => Auth::id(),
            ];
        }

        return null;
    }
}
