<?php

namespace App\Exports;

use App\Models\ExternalAttendanceTest;
use App\Models\ExternalAttendanceLink;
use App\Models\ExternalAttendanceSubmission;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExternalAttendanceCollegeLinksExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnWidths,
    WithStyles
{
    protected ExternalAttendanceTest $test;

    protected Collection $collegeStudentCounts;

    public function __construct(ExternalAttendanceTest $test)
    {
        $this->test = $test;

        // Attendance submission count and last activity per college.
        $this->collegeStudentCounts = ExternalAttendanceSubmission::where(
            'external_attendance_test_id',
            $test->id
        )
            ->selectRaw('college_id, COUNT(*) as total_students, MAX(created_at) as last_attempt')
            ->groupBy('college_id')
            ->get()
            ->keyBy('college_id');
    }

    public function collection()
    {
        // IMPORTANT: Same ordering as Attendance browser links page.
        return ExternalAttendanceLink::with('college')
            ->where('external_attendance_test_id', $this->test->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get()
            ->values()
            ->map(function ($link, $index) {
                $link->export_row_number = $index + 1;

                return $link;
            });
    }

    public function headings(): array
    {
        return [
            'Sr. No.',
            'College Name',
            'Student Count',
            'Last Activity',
            'Link Added Date',
            'Test Link',
            'Status',
            'Valid From',
            'Valid Until',
        ];
    }

    public function map($link): array
    {
        $stats = $this->collegeStudentCounts->get($link->college_id);

        return [
            $link->export_row_number,

            $link->college->full_name ?? '-',

            (int) ($stats->total_students ?? 0),

            $stats && $stats->last_attempt
                ? Carbon::parse($stats->last_attempt)->format('d M Y h:i A')
                : '-',

            $link->created_at
                ? Carbon::parse($link->created_at)->format('d M Y h:i A')
                : '-',

            route('form.view', $link->slug),

            $link->is_enabled ? 'Enabled' : 'Disabled',

            $link->valid_from
                ? Carbon::parse($link->valid_from)->format('d M Y h:i A')
                : '-',

            $link->valid_until
                ? Carbon::parse($link->valid_until)->format('d M Y h:i A')
                : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 35,
            'C' => 16,
            'D' => 24,
            'E' => 24,
            'F' => 60,
            'G' => 15,
            'H' => 24,
            'I' => 24,
        ];
    }

    public function styles($sheet)
    {
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);

        $sheet->getStyle('F')->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP);

        return [];
    }
}
