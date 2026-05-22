<?php

namespace App\Exports;

use App\Models\PartTimeJob;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PartTimeJobExport implements FromCollection, WithHeadings,ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
{
    $jobs = PartTimeJob::query();

    if ($this->request->job_type) {
        $jobs->where('job_type', 'like', '%' . $this->request->job_type . '%');
    }

    if ($this->request->shift) {
        $jobs->where('shift', 'like', '%' . $this->request->shift . '%');
    }

    if ($this->request->location) {
        $jobs->where('location', 'like', '%' . $this->request->location . '%');
    }

    if ($this->request->status) {
        $jobs->where('status', $this->request->status);
    }

    return $jobs->orderBy('id', 'desc')
        ->get()
        ->map(function ($row) {

            return [
                'ID'         => $row->id,
                'Name'       => $row->name,
                'Job Type'   => $row->job_type,
                'Shift'      => $row->shift,
                'Location'   => $row->location,
                'Mobile'     => $row->mobile,
                'Email'      => $row->email,
                'Status'     => $row->status,
                'Created At' => optional($row->created_at)->format('d-m-Y'),
            ];
        });
}

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Job Type',
            'Shift',
            'Location',
            'Mobile',
            'Email',
            'Status',
            'Created At',
        ];
    }
}