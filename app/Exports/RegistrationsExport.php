<?php

namespace App\Exports;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RegistrationsExport implements FromCollection, WithHeadings
{
    protected $pendingOnly;

    public function __construct($pendingOnly = false)
    {
        $this->pendingOnly = $pendingOnly;
    }

    public function collection()
    {
        $query = Registration::with('enquiry.student');

        if ($this->pendingOnly) {
            $query->whereDoesntHave('enquiry.student');
        }

        return $query->get()->map(function ($reg) {
            return [
                'Name'          => $reg->enquiry->name,
                'Mobile'        => $reg->enquiry->mobile,
                'Email'         => $reg->enquiry->email,
                'Amount Paid'   => $reg->amount_paid,
                'Payment Mode'  => $reg->payment_mode,
                'Registered At' => $reg->registered_at,
                'Status'        => $reg->enquiry->student ? 'Converted' : 'Pending',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Mobile',
            'Email',
            'Amount Paid',
            'Payment Mode',
            'Registered At',
            'Status'
        ];
    }
}
