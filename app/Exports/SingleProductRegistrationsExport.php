<?php

namespace App\Exports;

use App\Models\SingleProductRegistration;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SingleProductRegistrationsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        return SingleProductRegistration::with('courseData')
            ->when($this->request->technology, function ($query) {
                $query->where('technology', $this->request->technology);
            })
            ->when($this->request->slug, function ($query) {
                $query->where('slug', $this->request->slug);
            })
            ->when($this->request->limit, function ($query) {
                $query->limit((int) $this->request->limit);
            })
            ->latest()
            ->get()
            ->map(function ($row) {
                return [
                    'Full Name'   => $row->full_name,
                    'Email'      => $row->email,
                    'Phone'      => "'".$row->phone,
                    'Location'   => $row->location,
                    'Technology' => $row->technology ?? '-',
                    'Message'    => $row->message,
                    'Date' => optional($row->created_at)->format('d F Y'),
                ];
            });
    }


    public function headings(): array
    {
        return [
            'Client Name',
            'Email',
            'Phone',
            'Location',
            'Technology',
            // 'Slug',
            'Message',
            // 'IP Address',
            'Created At',
        ];
    }
}
