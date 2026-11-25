<?php

namespace App\Helpers;

use App\Models\InternStudent;

class InternsHelper
{
    /**
     * Check whether mobile exists in internship records.
     */
    public static function exists($mobile)
    {
        if (empty($mobile)) {
            return false;
        }

        $mobile = preg_replace('/\D+/', '', $mobile);

        return InternStudent::where(function ($query) use ($mobile) {
            $query->where('contact', $mobile)
                  ->orWhere('alternative_phone', $mobile);
        })->exists();
    }
}
