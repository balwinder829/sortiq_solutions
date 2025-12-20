<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadImport implements ToModel, WithHeadingRow
{
    protected $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }
    public function model(array $row)
    {
        // Skip row if phone is missing
        if (!isset($row['phone']) || empty($row['phone'])) {
            return null;
        }

        // OPTIONAL: skip duplicate phone numbers
        if (Lead::where('phone', $row['phone'])->exists()) {
            return null;
        }

        return new Lead([
            'name'        => $row['name'] ?? null,
            'email'       => $row['email'] ?? null,
            'phone'       => $row['phone'] ?? null,
            'source'      => $row['source'] ?? 'Excel Import',
            'status'      => 'new',
            'assigned_to' => null,
            'created_by'  => Auth::id(),
            'batch_id'    => $this->batchId,
        ]);
    }
}
