<?php

namespace App\Exports;

use App\Models\ProductsRegistration;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsRegistrationsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        return ProductsRegistration::with('courseData')

            /*
            |--------------------------------------------------------------------------
            | Export Selected Rows
            |--------------------------------------------------------------------------
            */
            ->when($this->request->ids, fn ($q) =>

                $q->whereIn('id', $this->request->ids)

            )

            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */
            ->when($this->request->technology, function ($query) {

                $query->where('technology', $this->request->technology);

            })

            ->when($this->request->slug, function ($query) {

                $query->where('slug', $this->request->slug);

            })

            /*
            |--------------------------------------------------------------------------
            | Date Filters
            |--------------------------------------------------------------------------
            */

            ->when($this->request->date_filter == 'today', fn ($q) =>

                $q->whereDate('created_at', today())

            )

            ->when($this->request->date_filter == 'yesterday', fn ($q) =>

                $q->whereDate('created_at', today()->subDay())

            )

            ->when($this->request->date_filter == 'this_week', fn ($q) =>

                $q->whereBetween('created_at', [

                    now()->startOfWeek(),
                    now()->endOfWeek()

                ])

            )

            ->when($this->request->date_filter == 'last_week', fn ($q) =>

                $q->whereBetween('created_at', [

                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek()

                ])

            )

            ->when($this->request->date_filter == 'this_month', fn ($q) =>

                $q->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year)

            )

            ->when(

                $this->request->date_filter == 'custom'
                && $this->request->from_date
                && $this->request->to_date,

                fn ($q) => $q->whereBetween('created_at', [

                    \Carbon\Carbon::parse($this->request->from_date)->startOfDay(),

                    \Carbon\Carbon::parse($this->request->to_date)->endOfDay()

                ])
            )

            /*
            |--------------------------------------------------------------------------
            | Limit
            |--------------------------------------------------------------------------
            */

            ->when($this->request->limit, fn ($q) =>

                $q->limit(min((int)$this->request->limit, 100))

            )

            ->latest()

            ->get()

            ->map(function ($row) {

                return [

                    'Full Name'   => $row->full_name,

                    'Email'       => $row->email,

                    'Phone'       => "'" . $row->phone,

                    'Location'    => $row->location,

                    'Technology'  => $row->technology ?? '-',

                    'Message'     => $row->message,

                    'Created At'  => optional($row->created_at)->format('d-m-Y'),

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
