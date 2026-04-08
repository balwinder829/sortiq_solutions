<?php

namespace App\Observers;

class PhoneNumberObserver
{
    public function saving($model)
    {
        if (method_exists($model, 'checkBlockedNumbers')) {
            $model->checkBlockedNumbers();
        }
    }
}