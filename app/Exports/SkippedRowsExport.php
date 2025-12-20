<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class SkippedRowsExport implements FromArray
{
    protected $warnings;

    public function __construct(array $warnings)
    {
        $this->warnings = $warnings;
    }

    public function array(): array
    {
        $data[] = ['Row', 'Reason', 'Value'];

        foreach ($this->warnings as $w) {
            $data[] = [$w['row'], $w['reason'], $w['value']];
        }

        return $data;
    }
}
