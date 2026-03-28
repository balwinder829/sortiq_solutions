<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;


class FormEntriesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    public function collection()
    {
        $allData = [];
        $page = 1;

        do {
            $response = Http::get('https://sortiqsolutions.com/wp-json/seocify/v1/submissions', [
                'form_id' => 5101,
                'page'    => $page,
                'per_page'=> 100,
            ]);

            $res = $response->json();

            foreach ($res['data'] ?? [] as $entry) {
                $fields = $entry['fields'] ?? [];

                $allData[] = [
                    $entry['date'],
                    $fields['your-name'] ?? '-',
                    $fields['email'] ?? '-',
                    // $fields['intl_tel-642'] ?? '-',
                     "\t" . ($fields['intl_tel-642'] ?? '-'), // ✅ BEST FIX
                    $fields['message'] ?? '',
                ];
            }

            $page++;

        } while (!empty($res['data']));

        return new Collection($allData);
    }

    public function headings(): array
    {
        return ['Date', 'Name', 'Email', 'Phone', 'Message'];
    }

     public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT, // Phone column
        ];
    }
}