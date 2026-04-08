<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\BlockedNumber;

class NotBlockedNumber implements Rule
{
    protected $messageText = '';

    public function passes($attribute, $value)
    {
        $numbers = explode(',', $value);
        $validNumbers = [];

        foreach ($numbers as $number) {
            $number = preg_replace('/\D/', '', trim($number));

            if ($number) {
                $validNumbers[] = $number;
            }
        }

        foreach ($validNumbers as $number) {

            $blocked = BlockedNumber::where('number', $number)
                ->whereNull('deleted_at')
                ->exists();

            if ($blocked) {

                // ✅ Dynamic message
                if (count($validNumbers) === 1) {
                    $this->messageText = "This number is in block list, so we cannot add this number.";
                } else {
                    $this->messageText = "One or more numbers are in block list, so we cannot add them.";
                }

                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return $this->messageText ?: 'Invalid number.';
    }
}