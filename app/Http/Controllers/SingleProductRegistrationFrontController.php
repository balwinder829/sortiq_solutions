<?php

namespace App\Http\Controllers;

use App\Models\SingleProductRegistration;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Exports\ServicesRegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\NotBlockedNumber;
 use App\Http\DataTables\DataTablesServerSide;

class SingleProductRegistrationFrontController extends Controller
{

    public function store(Request $request)
    {
        // dd($request);
        $data = $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone' => ['required', 'string', new NotBlockedNumber],
            'location'   => 'required|string|max:255',
            'technology' => 'required|string|max:255',
            'message'    => 'nullable|string',
            'slug'       => 'required|string|max:255',
        ]);

        // Prevent duplicate email + technology
        $exists = SingleProductRegistration::where('email', $data['email'])
            ->where('technology', $data['technology'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['email' => 'This email is already registered for this technology.'])
                ->withInput();
        }

        // Add server-side data (SAFE)
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr($request->userAgent(), 0, 500);

        SingleProductRegistration::create($data);

        return back()->with('success', 'Request added successfully');
    }
}
