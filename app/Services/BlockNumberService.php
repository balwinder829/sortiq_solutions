<?php

namespace App\Services;

use App\Models\{
    Enquiry,
    Student,
    BlockedNumber,
    BlockedNumberLog
};
use Illuminate\Support\Facades\DB;

class BlockNumberService
{
    /**
     * Model configuration
     */
    protected array $models = [
        'enquiries' => [
            'model'  => Enquiry::class,
            'column' => 'mobile',
            'parent' => true,
        ],
        'students' => [
            'model'  => Student::class,
            'column' => 'contact',
            'parent' => false,
        ],
    ];

    public function block(string $number): BlockedNumber
{
    return DB::transaction(function () use ($number) {

        /** -------------------------
         * 1️⃣ COUNT OCCURRENCES
         * ------------------------- */
        $enquiryCount = Enquiry::where('mobile', $number)->count();
        $studentCount = Student::where('contact', $number)->count();

        $totalCount = $enquiryCount + $studentCount;

        /** -------------------------
         * 2️⃣ CREATE BLOCKED NUMBER
         * ------------------------- */
        $blocked = BlockedNumber::create([
            'number'           => $number,
            'occurrence_count' => $totalCount,
            'blocked_at'       => now(),
        ]);

        /** -------------------------
         * 3️⃣ CREATE AUDIT LOGS
         * ------------------------- */
        if ($enquiryCount > 0) {
            BlockedNumberLog::create([
                'blocked_number_id' => $blocked->id,
                'table_name'        => 'enquiries',
                'count'             => $enquiryCount,
            ]);
        }

        if ($studentCount > 0) {
            BlockedNumberLog::create([
                'blocked_number_id' => $blocked->id,
                'table_name'        => 'students',
                'count'             => $studentCount,
            ]);
        }

        /** -------------------------
         * 4️⃣ DELETE ENQUIRIES (CASCADE)
         * ------------------------- */
        Enquiry::where('mobile', $number)
            ->chunk(100, function ($rows) {
                $rows->each->delete();
            });

        /** -------------------------
         * 5️⃣ DELETE ENQUIRY ASSIGNMENTS
         * ------------------------- */
        $enquiryIds = Enquiry::withTrashed()
            ->where('mobile', $number)
            ->pluck('id');

        if ($enquiryIds->isNotEmpty()) {
            DB::table('enquiry_assignments')
                ->whereIn('enquiry_id', $enquiryIds)
                ->update(['deleted_at' => now()]);
        }

        /** -------------------------
         * 6️⃣ DELETE STUDENTS
         * ------------------------- */
        Student::where('contact', $number)->delete();

        return $blocked;
    });
}


    public function block12(string $number): BlockedNumber
	{
	    return DB::transaction(function () use ($number) {

	        /** 1️⃣ Enquiry (cascade handles followups, activities, registration) */
	        Enquiry::where('mobile', $number)
	            ->chunk(100, function ($rows) {
	                $rows->each->delete();
	            });

	        /** 2️⃣ enquiry_assignments (no model) */
	        $enquiryIds = Enquiry::withTrashed()
	            ->where('mobile', $number)
	            ->pluck('id');

	        if ($enquiryIds->isNotEmpty()) {
	            DB::table('enquiry_assignments')
	                ->whereIn('enquiry_id', $enquiryIds)
	                ->update(['deleted_at' => now()]);
	        }

	        /** 3️⃣ Student (independent domain) */
	        Student::where('contact', $number)->delete();

	        /** 4️⃣ Save audit */
	        return BlockedNumber::create([
	            'number'           => $number,
	            'blocked_at'       => now(),
	            'occurrence_count' => $enquiryIds->count(),
	        ]);
	    });
	}


    public function old_block_old(string $number): BlockedNumber
    {
        return DB::transaction(function () use ($number) {

            $tableCounts = [];
            $total = 0;

            /**
             * 1️⃣ Count occurrences
             */
            foreach ($this->models as $table => $config) {
                $count = $config['model']::where($config['column'], $number)->count();
                $tableCounts[$table] = $count;
                $total += $count;
            }

            /**
             * 2️⃣ Save blocked number
             */
            $blocked = BlockedNumber::create([
                'number'           => $number,
                'occurrence_count' => $total,
                'blocked_at'       => now(),
            ]);

            /**
             * 3️⃣ Save audit logs
             */
            foreach ($tableCounts as $table => $count) {
                if ($count > 0) {
                    BlockedNumberLog::create([
                        'blocked_number_id' => $blocked->id,
                        'table_name'        => $table,
                        'count'             => $count,
                    ]);
                }
            }

            /**
             * 4️⃣ SOFT DELETE RECORDS
             */

            /** 🔥 4.1 Enquiry (parent + cascade) */
            $enquiryIds = Enquiry::where('mobile', $number)
                ->pluck('id')
                ->toArray();

            Enquiry::whereIn('id', $enquiryIds)
                ->chunk(100, function ($rows) {
                    $rows->each->delete(); // triggers cascade
                });

            /** 🔥 4.2 enquiry_assignments (NO MODEL) */
            if (!empty($enquiryIds)) {
                DB::table('enquiry_assignments')
                    ->whereIn('enquiry_id', $enquiryIds)
                    ->update(['deleted_at' => now()]);
            }

            /** 🔥 4.3 Student (single table) */
            Student::where('contact', $number)->delete();

            return $blocked;
        });
    }

    public function unblock(BlockedNumber $blocked): void
    {
        DB::transaction(function () use ($blocked) {

            /** 🔁 Restore Enquiry */
            $enquiryIds = Enquiry::withTrashed()
                ->where('mobile', $blocked->number)
                ->pluck('id')
                ->toArray();

            Enquiry::withTrashed()
                ->whereIn('id', $enquiryIds)
                ->chunk(100, function ($rows) {
                    $rows->each->restore();
                });

            /** 🔁 Restore enquiry_assignments */
            if (!empty($enquiryIds)) {
                DB::table('enquiry_assignments')
                    ->whereIn('enquiry_id', $enquiryIds)
                    ->update(['deleted_at' => null]);
            }

            /** 🔁 Restore Student */
            Student::withTrashed()
                ->where('contact', $blocked->number)
                ->restore();

            $blocked->delete();
        });
    }
}
