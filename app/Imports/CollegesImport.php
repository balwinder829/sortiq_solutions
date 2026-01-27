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
            $result = $this->resolver->resolveForImport($combined);

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
}
