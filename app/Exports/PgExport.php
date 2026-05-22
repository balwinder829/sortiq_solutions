<?php

namespace App\Exports;

use App\Models\Pg;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PgExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $pgs = Pg::query();

        if ($this->request->pg_type) {
            $pgs->whereIn('pg_type', [$this->request->pg_type, 'both']);
        }

        if ($this->request->food_type) {
            $pgs->where('food_type', $this->request->food_type);
        }

        if ($this->request->status) {
            $pgs->where('status', $this->request->status);
        }

        if ($this->request->address) {
            $pgs->where('address', 'like', '%' . $this->request->address . '%');
        }

        return $pgs->orderBy('id', 'desc')
            ->get()
            ->map(function ($row) {

                return [
                    'ID'          => $row->id,
                    'Name'        => $row->name,
                    'PG Type'     => $row->pg_type,
                    'Food Type'   => $row->food_type,
                    'Address'     => $row->address,
                    'Contact'     => $row->contact,
                    'Email'       => $row->email,
                    'Status'      => $row->status,
                    'Created At'  => optional($row->created_at)->format('d-m-Y'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'PG Type',
            'Food Type',
            'Address',
            'Contact',
            'Email',
            'Status',
            'Created At',
        ];
    }
}