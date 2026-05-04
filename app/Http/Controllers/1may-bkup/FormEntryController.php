<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\FormEntriesExport;
use Maatwebsite\Excel\Facades\Excel;

class FormEntryController extends Controller
{

    public function __construct()
    {
       
        $this->middleware('permission:gmail_form_entries.view')->only(['index','data','export']);
       
    }
     public function index()
    {
        return view('form-entries.index');
    }

    public function data(Request $request)
    {
        $start  = $request->start ?? 0;
        $length = $request->length ?? 10;
        $page   = ($start / $length) + 1;

        $response = Http::get('https://sortiqsolutions.com/wp-json/seocify/v1/submissions', [
            'form_id' => 5101,
            'page'    => $page,
            'per_page'=> $length,
        ]);

        $res = $response->json();

        $data = [];

        foreach ($res['data'] ?? [] as $index => $entry) {
            $fields = $entry['fields'] ?? [];

            $message = $fields['message'] ?? '';

            $data[] = [
                $start + $index + 1,
                $entry['date'],
                $fields['your-name'] ?? '-',
                $fields['email'] ?? '-',
                $fields['intl_tel-642'] ?? '-',

                // Message column with button
                \Illuminate\Support\Str::limit($message, 30),

                $message
                ? '<button class="btn btn-sm view-message" title="View Message" data-message="'.e($message).'"><i class="fa fa-eye"></i></button>'
                : '-'
            ];
        }

        return response()->json([
            "draw"            => intval($request->draw),
            "recordsTotal"    => $res['total'] ?? 0,
            "recordsFiltered" => $res['total'] ?? 0,
            "data"            => $data,
        ]);
    }

    public function export()
    {
        return Excel::download(new FormEntriesExport, 'form_entries.xlsx');
    }
}
