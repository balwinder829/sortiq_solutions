<?php

namespace App\Exports;

use App\Models\InternshipRegistration;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InternshipRegistrationsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    protected ?Collection $exportedCollection = null;

    protected array $exportedIds = [];

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        // Return cached collection if already prepared
        if ($this->exportedCollection !== null) {
            return $this->exportedCollection;
        }

        $query = InternshipRegistration::with([
            'collegeData',
            'courseData'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Selected Export
        |--------------------------------------------------------------------------
        | When selected IDs are provided, export only those records.
        | Ignore all other filters and limits.
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

            // Page Type
            if ($this->request->filled('page_type')) {
                $query->where('page_type', $this->request->page_type);
            }

            // College
            if ($this->request->filled('college')) {
                $query->where('college_name', $this->request->college);
            }

            // Technology
            if ($this->request->filled('technology')) {
                $query->where('technology', $this->request->technology);
            }

            // Slug
            if ($this->request->filled('slug')) {
                $query->where('slug', $this->request->slug);
            }

            // Status
            if ($this->request->filled('status')) {
                $query->where('status', $this->request->status);
            }

            // Download Status
            if ($this->request->download_status === 'downloaded') {

                $query->whereNotNull('downloaded_at');

            } elseif ($this->request->download_status === 'not_downloaded') {

                $query->whereNull('downloaded_at');
            }

            // Date Filters
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

                    if (
                        $this->request->from_date &&
                        $this->request->to_date
                    ) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($this->request->from_date)->startOfDay(),
                            Carbon::parse($this->request->to_date)->endOfDay()
                        ]);
                    }

                    break;
            }

            // Limit only for normal export
            if ($this->request->filled('limit')) {
                $query->limit(min((int) $this->request->limit, 100));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch Records
        |--------------------------------------------------------------------------
        */

        $records = $query->latest()->get();

        // Store exact IDs included in this export
        $this->exportedIds = $records->pluck('id')->toArray();

        /*
        |--------------------------------------------------------------------------
        | Prepare Excel Collection
        |--------------------------------------------------------------------------
        */

        $this->exportedCollection = $records->map(function ($row) {

            return [
                'Full Name' => $row->full_name,

                'Email' => $row->email,

                'Phone' => $row->phone,

                'College' => optional($row->collegeData)->college_display_name
                    ?? $row->college_name
                    ?? '-',

                'Technology' => optional($row->courseData)->course_name
                    ?? $row->technology
                    ?? '-',

                'Status' => $row->status,

                'Message' => $row->message,

                'Created At' => optional($row->created_at)->format('d-m-Y'),
            ];

        });

        return $this->exportedCollection;
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
            'Full Name',
            'Email',
            'Phone',
            'College',
            'Technology',
            'Status',
            'Message',
            'Submit Date',
        ];
    }
}
