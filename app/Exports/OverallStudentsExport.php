<?php

namespace App\Exports;

use App\Models\StudentTest;
use App\Models\OfflineTestStudent;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping
};

class OverallStudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
{
    $mode = $this->filters['mode'] ?? null;

    $online = collect();
    $offline = collect();

    /* ================= ONLINE ================= */
    if ($mode === null || $mode === 'online') {
        $online = StudentTest::query()
            ->with('test.category')
            ->when($this->filters['finalized'] ?? false, fn($q) => $q->where('is_finalized', 1))
            ->when($this->filters['category_id'] ?? null, function ($q) {
                $q->whereHas('test', fn($t) =>
                    $t->where('test_category_id', $this->filters['category_id'])
                );
            })
            ->get()
            ->map(fn($s) => [
                'mode'      => 'Online',
                'test'      => $s->test->title ?? '',
                'category'  => $s->test->category->name ?? '',
                'name'      => $s->student_name ?? '-',
                'email'     => $s->student_email ?? '-',
                'mobile'    => $s->student_mobile ?? '-',
                'gender'    => $this->formatGender($s),
                'score'     => $s->score ?? '-',
                'finalized' => $s->is_finalized ? 'Yes' : 'No',
            ]);
    }

    /* ================= OFFLINE ================= */
    if ($mode === null || $mode === 'offline') {
        $offline = OfflineTestStudent::query()
            ->with('test.category')
            ->when($this->filters['finalized'] ?? false, fn($q) => $q->where('is_finalized', 1))
            ->when($this->filters['category_id'] ?? null, function ($q) {
                $q->whereHas('test', fn($t) =>
                    $t->where('test_category_id', $this->filters['category_id'])
                );
            })
            ->get()
            ->map(fn($s) => [
                'mode'      => 'Offline',
                'test'      => $s->test->title ?? '',
                'category'  => $s->test->category->name ?? '',
                'name'      => $s->student_name ?? '-',
                'email'     => $s->student_email ?? '-',
                'mobile'    => $s->student_mobile ?? '-',
                'gender'    => $this->formatGender($s),
                'score'     => $s->score ?? '-',
                'finalized' => $s->is_finalized ? 'Yes' : 'No',
            ]);
    }

    return $online->merge($offline);
}

    public function collectionolj()
    {
        /* ================= ONLINE STUDENTS ================= */
        $online = StudentTest::query()
            ->with('test.category')
            ->when($this->filters['finalized'] ?? false, function ($q) {
                $q->where('is_finalized', 1);
            })
            ->when($this->filters['attempted'] ?? false, function ($q) {
                $q; // all attempted
            })
            ->when($this->filters['mode'] ?? null, function ($q) {
                $q->where('source', 'online');
            })
            ->when($this->filters['category_id'] ?? null, function ($q) {
                $q->whereHas('test', function ($t) {
                    $t->where('test_category_id', $this->filters['category_id']);
                });
            })
            ->get()
            ->map(function ($s) {
                return [
                    'mode'      => 'Online',
                    'test'      => $s->test->title ?? '',
                    'category'  => $s->test->category->name ?? '',
                    'name'      => $s->student_name ?? '-',
                    'email'     => $s->student_email ?? '-',
                    'mobile'    => $s->student_mobile ?? '-',
                    'gender'    => $this->formatGender($s),
                    'score'     => $s->score ?? '-',
                    'finalized' => $s->is_finalized ? 'Yes' : 'No',
                ];
            });

        /* ================= OFFLINE STUDENTS ================= */
        $offline = OfflineTestStudent::query()
            ->with('test.category')
            ->when($this->filters['finalized'] ?? false, function ($q) {
                $q->where('is_finalized', 1);
            })
            ->when($this->filters['attempted'] ?? false, function ($q) {
                $q; // all attempted
            })
            ->when($this->filters['mode'] ?? null, function ($q) {
                $q; // offline always offline
            })
            ->when($this->filters['category_id'] ?? null, function ($q) {
                $q->whereHas('test', function ($t) {
                    $t->where('test_category_id', $this->filters['category_id']);
                });
            })
            ->get()
            ->map(function ($s) {
                return [
                    'mode'      => 'Offline',
                    'test'      => $s->test->title ?? '',
                    'category'  => $s->test->category->name ?? '',
                    'name'      => $s->student_name ?? '-',
                    'email'     => $s->student_email ?? '-',
                    'mobile'    => $s->student_mobile ?? '-',
                    'gender'    => $this->formatGender($s),
                    'score'     => $s->score ?? '-',
                    'finalized' => $s->is_finalized ? 'Yes' : 'No',
                ];
            });

        return $online->merge($offline);
    }

    public function headings(): array
    {
        return [
            'Mode',
            'Test Title',
            'Category',
            'Student Name',
            'Email',
            'Mobile',
            'Gender',
            'Score',
            'Finalized',
        ];
    }

    public function map($row): array
    {
        return array_values($row);
    }

    /* ================= GENDER FORMATTER ================= */
    protected function formatGender($student): string
    {
        if (!isset($student->gender) || empty($student->gender)) {
            return '-';
        }

        return ucfirst(strtolower($student->gender));
    }
}
