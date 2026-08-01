<?php

namespace App\Imports;

use App\Services\CollegeResolver;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CollegesImport implements ToCollection, WithHeadingRow
{
    protected CollegeResolver $resolver;

    public int $created = 0;
    public int $skipped = 0;

    public array $skippedRows = [];

    public function __construct(CollegeResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            if (
                empty($row['college_name']) ||
                empty($row['district']) ||
                empty($row['state'])
            ) {
                continue;
            }

            // Build combined string for resolver
            $combined = trim(
                $row['college_name'] . ', ' .
                $row['district'] . ', ' .
                $row['state']
            );

            // Import-specific resolver
            // $result = $this->resolver->resolveForImport($combined);
            // Import-specific resolver
            $result = $this->resolver->resolveForImport($combined, [

                'college_type' => $this->getCollegeType($row['college_type'] ?? ''),

                'offer_training' => strtolower(trim($row['providing_training'] ?? '')) === 'yes' ? 1 : 0,

                'training_in_year' => (int)($row['no_of_times_in_year'] ?? 0),

                'is_important' => strtolower(trim($row['important'] ?? '')) === 'yes' ? 1 : 0,

                'ownership_type' => strtolower(trim($row['ownership'] ?? '')) === 'government' ? 1 : 0,

                'connection_type' => in_array(
                    strtolower(trim($row['connection'] ?? '')),
                    ['old', 'old connection']
                ) ? 1 : 0,

                'departments' => !empty($row['departments'])
                    ? array_map('trim', explode(',', $row['departments']))
                    : [],
            ]);

            if ($result['status'] === 'created') {
                $this->created++;
            } else {
                $this->skipped++;
                $this->skippedRows[] = [
                    'row' => $index + 2, // +2 because heading + 0 index
                    'college' => $row['college_name'],
                    'district' => $row['district'],
                    'state' => $row['state'],
                ];
            }
        }
    }

    protected function getCollegeType($value): int
    {
        $value = strtolower(trim($value));

        return match ($value) {
            'degree' => 0,
            'diploma' => 1,
            'degree, diploma',
            'degree,diploma',
            'degree & diploma',
            'degree diploma' => 2,
            default => 0,
        };
    }
}
