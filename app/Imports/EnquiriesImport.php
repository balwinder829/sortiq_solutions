<?php

namespace App\Imports;

use App\Models\Enquiry;
use App\Models\BlockedNumber;
use App\Services\CollegeResolver;

use Throwable;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class EnquiriesImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsOnError,
    SkipsEmptyRows
{
    use SkipsFailures;

    protected $creator;
    protected $isPassout;

    // Counters
    public $totalRows = 0;
    public $insertedRows = 0;
    public $skippedRows = 0;

    // Warnings
    public $blockedNumbers = [];
    public $duplicateEntries = [];

    // Row tracker
    public $currentRow = 1;

    public function __construct($creator, $isPassout = 0)
    {
        $this->creator = $creator;
        $this->isPassout = $isPassout;
    }

    /* ================= MOBILE PARSER ================= */
    private function extractMobile($value)
    {
        if (empty($value)) return null;

        $numbers = preg_split('/[,\;\|\-\_]+/', $value);

        foreach ($numbers as $num) {
            $num = trim($num);
            $num = preg_replace('/\D/', '', $num);

            if (strlen($num) >= 10 && strlen($num) <= 15) {
                return $num;
            }
        }

        return null;
    }

    /* ================= VALIDATION ================= */
    public function rules(): array
    {
        return [
            '*.name'   => 'required|string|max:255',
            '*.mobile' => 'nullable',
            '*.email'  => 'nullable|email',

            '*.college' => function ($attribute, $value, $fail) {
                if (empty($value)) return;

                if (count(explode(',', $value)) < 3) {
                    $fail('Invalid college format. Use: College Name, District, State');
                }
            },

            '*.gap' => 'nullable|numeric|min:0'
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.name.required' => 'Name is required',
            '*.email.email'   => 'Invalid email format',
        ];
    }

    /* ================= EMPTY ROW EXTRA SAFETY ================= */
    public function isEmptyRow(array $row): bool
    {
        $filtered = collect($row)->filter(function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        return $filtered->isEmpty();
    }

    /* ================= MAIN ================= */
    public function model(array $row)
    {
        // dd($row);
        $this->totalRows++;

        // Track row number (Excel row starts from 2 due to heading)
        $this->currentRow++;
        $rowNumber = $this->currentRow;

        // Trim values
        $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

        // Extra empty row safety
        $nonEmpty = collect($row)->filter(fn($v) => !is_null($v) && trim($v) !== '');
        if ($nonEmpty->isEmpty()) {
            $this->skippedRows++;
            return null;
        }

        /* ===== MOBILE ===== */
        $mobile = $this->extractMobile($row['mobile'] ?? null);

        if (!empty($row['mobile']) && !$mobile) {
            $this->duplicateEntries[] = "Row {$rowNumber} – Invalid mobile format";
            $this->skippedRows++;
            return null;
        }

        /* ===== BLOCKED ===== */
        if (!empty($mobile) &&
            BlockedNumber::where('number', $mobile)->exists()
        ) {
            $this->blockedNumbers[] = "Row {$rowNumber} – Blocked number: {$mobile}";
            $this->skippedRows++;
            return null;
        }

        /* ===== DUPLICATE ===== */
        $exists = Enquiry::query()
            ->where('is_passout', $this->isPassout)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($mobile, $row) {
                if (!empty($mobile)) {
                    $q->where('mobile', $mobile);
                }
                if (!empty($row['email'])) {
                    $q->orWhere('email', $row['email']);
                }
            })
            ->exists();

        if ($exists) {
            $this->duplicateEntries[] = "Row {$rowNumber} – Duplicate skipped: {$mobile}";
            $this->skippedRows++;
            return null;
        }

        /* ===== COLLEGE ===== */
        $collegeId = null;

        if (!empty($row['college'])) {
            $college = app(CollegeResolver::class)->resolve($row['college']);
            $collegeId = $college->id;
        }

        /* ===== GAP ===== */
        $gap = null;

        if ($this->isPassout) {
            $gap = isset($row['gap']) && is_numeric($row['gap'])
                ? (int) $row['gap']
                : null;
        }

        /* ===== DEPARTMENTS ===== */
        $departments = null;

        /* ===== DEPARTMENTS ===== */
        $departments = $this->extractDepartments(
            $row['departments'] ?? null
        );
// dd($departments);
        $this->insertedRows++;

         // $activeSessionId = session('admin_session_id');
         $activeSessionId = session(
            'admin_header_session_id',
            session('admin_session_id')
        );

        return new Enquiry([
            'name'       => $row['name'],
            'mobile'     => $mobile,
            'email'      => $row['email'] ?? null,
            'college'    => $collegeId,
            'study'      => $row['study'] ?? null,
            'semester'   => $row['semester'] ?? null,
            'gap'        => $gap,
            'session_id'        => $activeSessionId,
            'created_by' => $this->creator,
            'source'     => 'excel',
            'is_passout' => $this->isPassout,
             'departments' => $departments,
        ]);
    }

    /* ================= DEPARTMENT PARSER ================= */
private function extractDepartments($value): ?array
{
    // Already an array
    if (is_array($value)) {
        return array_values(array_filter(
            array_map(
                fn($item) => trim((string) $item),
                $value
            ),
            fn($item) => $item !== ''
        ));
    }

    if ($value === null) {
        return null;
    }

    $value = trim((string) $value);

    if ($value === '') {
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Clean Excel / CSV quoting
    |--------------------------------------------------------------------------
    */

    // Remove outer quotes repeatedly:
    // "abc"       -> abc
    // '"abc"'     -> abc
    // "[...]"     -> [...]
    for ($i = 0; $i < 2; $i++) {

        if (
            strlen($value) >= 2 &&
            (
                ($value[0] === '"' && substr($value, -1) === '"') ||
                ($value[0] === "'" && substr($value, -1) === "'")
            )
        ) {
            $value = substr($value, 1, -1);
            $value = trim($value);
        }
    }

    // Convert escaped quotes:
    // [\"MBA\",\"Civil\"] -> ["MBA","Civil"]
    $value = str_replace(['\\"', "\\'"], ['"', "'"], $value);

    /*
    |--------------------------------------------------------------------------
    | Try JSON
    |--------------------------------------------------------------------------
    */

    $decoded = json_decode($value, true);

    if (json_last_error() === JSON_ERROR_NONE) {

        // JSON array
        if (is_array($decoded)) {

            return array_values(array_filter(
                array_map(
                    fn($item) => trim(
                        trim((string) $item),
                        "\"' "
                    ),
                    $decoded
                ),
                fn($item) => $item !== ''
            ));
        }

        // JSON string:
        // "abc" -> abc
        if (is_string($decoded) && trim($decoded) !== '') {
            $value = trim($decoded);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Remove [ ] if it looks like an array but was not valid JSON
    |--------------------------------------------------------------------------
    |
    | Example:
    | [MBA,Civil,EC]
    |
    */

    if (
        strlen($value) >= 2 &&
        $value[0] === '[' &&
        substr($value, -1) === ']'
    ) {
        $value = substr($value, 1, -1);
        $value = trim($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Normal text / comma / semicolon / pipe / new line
    |--------------------------------------------------------------------------
    |
    | abc
    | xyz,123
    | MBA,Civil,EC
    | MBA;Civil;EC
    | MBA|Civil|EC
    |
    */

    $departments = preg_split(
        '/\s*[,;|\r\n]+\s*/',
        $value
    );

    $departments = array_map(
        fn($item) => trim($item, " \t\n\r\0\x0B\"'[]"),
        $departments
    );

    $departments = array_values(array_filter(
        $departments,
        fn($item) => $item !== ''
    ));

    return !empty($departments) ? $departments : null;
}
    public function onError(Throwable $e)
    {
        // silently ignore system errors
    }
}