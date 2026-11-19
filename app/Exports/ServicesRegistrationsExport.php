<?php

namespace App\Exports;

use App\Models\ServicesRegistration;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ServicesRegistrationsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    protected array $exportedIds = [];

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $query = ServicesRegistration::with('courseData');

        /*
        |--------------------------------------------------------------------------
        | Selected Export
        |--------------------------------------------------------------------------
        | If IDs are provided, export ONLY those selected records.
        | Ignore all other filters in this case.
        */

        if ($this->request->filled('ids')) {

            $ids = array_filter((array) $this->request->input('ids'));

            $query->whereIn('id', $ids);

        } else {

            /*
            |--------------------------------------------------------------------------
            | Normal Export Filters
            |--------------------------------------------------------------------------
            */

            // Technology filter
            if ($this->request->filled('technology')) {
                $query->where('technology', $this->request->technology);
            }

            // Slug filter
            if ($this->request->filled('slug')) {
                $query->where('slug', 'like', '%' . $this->request->slug . '%');
            }

            // Download status filter
            if ($this->request->download_status === 'downloaded') {
                $query->whereNotNull('downloaded_at');

            } elseif ($this->request->download_status === 'not_downloaded') {
                $query->whereNull('downloaded_at');
            }

            // Date filters
            switch ($this->request->date_filter) {

                case 'today':
                    $query->whereDate('created_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;

                case 'this_week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;

                case 'last_week':
                    $query->whereBetween('created_at', [
                        now()->subWeek()->startOfWeek(),
                        now()->subWeek()->endOfWeek()
                    ]);
                    break;

                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;

                case 'custom':

                    if ($this->request->from_date && $this->request->to_date) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($this->request->from_date)->startOfDay(),
                            Carbon::parse($this->request->to_date)->endOfDay()
                        ]);
                    }

                    break;
            }

            // Limit applies only to normal export
            if ($this->request->filled('limit')) {
                $query->limit(min((int) $this->request->limit, 100));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch Records
        |--------------------------------------------------------------------------
        */

        $registrations = $query->latest()->get();

        // Store exact IDs included in this export
        $this->exportedIds = $registrations->pluck('id')->all();

        /*
        |--------------------------------------------------------------------------
        | Excel Rows
        |--------------------------------------------------------------------------
        */

        return $registrations->map(function ($row) {

            return [
                'Full Name'  => $row->full_name,
                'Email'      => $row->email,
                'Phone'      => "'" . $row->phone,
                'Location'   => $row->location,
                'Technology' => optional($row->courseData)->course_name
                                    ?? $row->technology
                                    ?? '-',
                'Message'    => $row->message,
                'Created At' => optional($row->created_at)->format('d-m-Y'),
            ];

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Get Exported IDs
    |--------------------------------------------------------------------------
    */

    public function getExportedIds(): array
    {
        return $this->exportedIds;
    }

    /*
    |--------------------------------------------------------------------------
    | Excel Headings
    |--------------------------------------------------------------------------
    */

    public function headings(): array
    {
        return [
            'Client Name',
            'Email',
            'Phone',
            'Location',
            'Technology',
            'Message',
            'Created At',
        ];
    }
}
