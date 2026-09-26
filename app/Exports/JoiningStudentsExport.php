<?php

namespace App\Exports;

use App\Models\JoiningStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class JoiningStudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = JoiningStudent::with([
            'collegeData',
            'courseData',
            'durationData',
        ]);

        // Existing filters
        if ($this->request->filled('student_name')) {
            $query->where(
                'student_name',
                'like',
                '%' . $this->request->student_name . '%'
            );
        }

        if ($this->request->filled('college')) {
            $query->where('college', $this->request->college);
        }

        if ($this->request->filled('technology')) {
            $query->where('technology', $this->request->technology);
        }

        if (
            $this->request->has('is_sent') &&
            $this->request->is_sent !== ''
        ) {
            $query->where(
                'is_sent_to_detail',
                (int) $this->request->is_sent
            );
        }

        // Payment status filter
        if ($this->request->filled('payment_status')) {
            $query->where(
                'payment_status',
                $this->request->payment_status
            );
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Father Name',
            'College',
            'Duration',
            'Technology',
            'Date of Joining',
            'Created At',
            'Student Status',

            // Payment details
            'Payment Amount',
            'Payment Status',
            'UPI Account ID',
            'Transaction / UTR ID',
            'Payment Date',
            'Payment Proof',
            'Payment Verified At',
            'Payment Verified By',
            'Payment Admin Note',
        ];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->student_name,
            $data->father_name,
            $data->collegeData->FullName ?? '',
            $data->durationData->name ?? '',
            $data->courseData->course_name ?? '',

            $this->formatDate($data->date_of_joining, 'd-m-Y'),
            $this->formatDate($data->created_at, 'd-m-Y H:i'),

            $data->is_sent_to_detail ? 'Sent' : 'Not Sent',

            // Payment details
            $data->payment_amount,
            ucfirst($data->payment_status ?? 'pending'),
            $data->payment_upi_account_id,
            $data->payment_transaction_id,
            $this->formatDate($data->payment_date, 'd-m-Y'),
            $data->payment_proof,
            $this->formatDate($data->payment_verified_at, 'd-m-Y H:i'),
            $data->payment_verified_by,
            $data->payment_admin_note,
        ];
    }

    private function formatDate($value, string $format): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return Carbon::parse($value)->format($format);
        } catch (\Throwable $e) {
            return '';
        }
    }
}