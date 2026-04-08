<?php

namespace App\Traits;

trait HasPhoneNumberTrait
{
    public function getPhoneFields(): array
    {
        return property_exists($this, 'phoneFields')
            ? $this->phoneFields
            : [];
    }
}