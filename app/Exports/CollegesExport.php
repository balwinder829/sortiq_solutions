<?php

namespace App\Exports;

use App\Models\College;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
// use Maatwebsite\Excel\Concerns\WithShouldAutoSize;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CollegesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $stateName;
    protected $districtName;
    protected $studentFilter;
    protected $activeSessionId;
    protected $college_type;
    protected $offer_training;

    public function __construct($stateName = null, $districtName = null, $studentFilter = null, $college_type = null, $offer_training = null)
    {
        $this->stateName     = $stateName;
        $this->districtName  = $districtName;
        $this->studentFilter = $studentFilter;
        $this->college_type = $college_type;
        $this->offer_training = $offer_training;
        $this->activeSessionId = session('admin_session_id');


        // dd([
        //     'stateName' => $stateName,
        //     'districtName' => $districtName,
        //     'studentFilter' => $studentFilter,
        //     'college_type' => $college_type,
        //     'offer_training' => $offer_training,
        // ]);
    }

    public function collection()
    {
        $query = College::query()
            ->with(['state', 'district'])
            ->withCount([
                'students as students_count' => function ($q) {
                    $q->where('session', $this->activeSessionId);
                }
            ]);

        if (!empty($this->stateName)) {
            $query->whereHas('state', function ($q) {
                $q->where('name', $this->stateName);
            });
        }

        if (!empty($this->districtName)) {
            $query->whereHas('district', function ($q) {
                $q->where('name', $this->districtName);
            });
        }

        if (!empty($this->college_type)) {
            $query->where('college_type', $this->college_type);
        }

        if (!empty($this->offer_training)) {
            $query->where('offer_training', $this->offer_training);
        }



        if (!empty($this->studentFilter)) {

            if ($this->studentFilter === 'zero') {
                $query->having('students_count', 0);
            }

            if ($this->studentFilter === 'more') {
                $query->having('students_count', '>', 0);
            }

            if ($this->studentFilter === 'asc') {
                $query->orderBy('students_count', 'asc');
            }

            if ($this->studentFilter === 'desc') {
                $query->orderBy('students_count', 'desc');
            }
        }

        // dd($query->get());
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'College / Place Name',
            'State',
            'District',
            'Display Name',
             'College Type',
            'Providing Training',
            'Students Count',
        ];
    }

    public function map($college): array
    {
        $collegeType = $college->college_type == 0 ? 'Degree' : 'Diploma';
        $training = $college->offer_training == 1 ? 'Yes' : 'No';
        return [
            $college->college_name,
            $college->state->name ?? '-',
            $college->district->name ?? '-',
            $college->college_display_name,
            $collegeType,
            $training,
            $college->students_count ?? 0,
        ];
    }
}