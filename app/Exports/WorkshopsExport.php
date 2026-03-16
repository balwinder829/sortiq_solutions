<?php

namespace App\Exports;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WorkshopsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize
{
    protected $request;
    protected $activeSessionId;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->activeSessionId = session('admin_session_id');
    }

    public function collection()
    {
        $query = Workshop::with('college');
        if (!empty($this->activeSessionId)) {
            $query->where('session', $this->activeSessionId);
        }

        // ✅ College Filter
        if ($this->request->college_id) {
            $query->where('college_id', $this->request->college_id);
        }

        // State Filter
        if ($this->request->state_id) {
            $query->whereHas('college', function ($q) {
                $q->where('state_id', $this->request->state_id);
            });
        }

        // District Filter
        if ($this->request->district_id) {
            $query->whereHas('college', function ($q) {
                $q->where('district_id', $this->request->district_id);
            });
        }

        // College Type (Degree/Diploma)
        if ($this->request->college_type !== null && $this->request->college_type !== '') {
            $query->whereHas('college', function ($q) {
                $q->where('college_type', $this->request->college_type);
            });
        }

        // ✅ Status Filter
        if ($this->request->status) {
            $query->where('status', $this->request->status);
        }

        // ✅ Exact Date Filter
        if ($this->request->date) {
            $query->whereDate('date', $this->request->date);
        }

        // ✅ Range Filter
        if ($this->request->range) {

            if ($this->request->range == 'today') {
                $query->whereDate('date', now()->toDateString());
            }

            if ($this->request->range == 'upcoming') {
                $query->whereDate('date', '>', now()->toDateString());
            }

            if ($this->request->range == 'past') {
                $query->whereDate('date', '<', now()->toDateString());
            }
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'College',
             'College Type',
            'State',
            'District',
            'Type',
            'Event Type',
            'Duration',
            'TPO/HOD No',
            'Contact Name',
            'Date',
            'Status'
        ];
    }

    // public function map($workshop): array
    // {
    //     return [
    //         $workshop->id,
    //         $workshop->title,
    //         $workshop->college->college_name ?? '',
    //         $workshop->type,
    //         // $workshop->session,
    //         $workshop->duration,
    //         $workshop->tp_hod_no,
    //         $workshop->name,
    //         optional($workshop->date)->format('d-m-Y'),
    //         ucfirst($workshop->status),
    //     ];
    // }

    public function map($workshop): array
    {   
         $collegeType = optional($workshop->college)->college_type == 0 ? 'Degree' : 'Diploma';
        return [
            $workshop->id,
            $this->formatText($workshop->title),
            $this->formatText($workshop->college->college_name ?? ''),
            $collegeType,
            $this->formatText($workshop->college->state->name ?? ''),
            $this->formatText($workshop->college->district->name ?? ''),
            $this->formatText($workshop->type),
            $this->formatText($workshop->event_type),
            $this->formatText($workshop->duration),
            $this->formatText($workshop->tp_hod_no),
            $this->formatText($workshop->name),
            optional($workshop->date)->format('d-m-Y'),
            $this->formatText($workshop->status),
        ];
    }

    private function formatText($value)
    {
        return ucwords(strtolower($value ?? ''));
    }
}