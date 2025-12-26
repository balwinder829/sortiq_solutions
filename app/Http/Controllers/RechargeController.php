<?php

namespace App\Http\Controllers;

// use App\Http\Requests\StoreRechargeRequest;
use App\Http\Requests\UpdateRechargeRequest;
use App\Models\Recharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RechargeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // change if your module has different auth
    }

    public function index(Request $request)
    {
        $query = Recharge::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('mobile_number', 'like', "%{$q}%")
                    ->orWhere('employee_name', 'like', "%{$q}%")
                    ->orWhere('reference', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $recharges = $query->orderBy('created_at', 'desc')->paginate(100)->withQueryString();

        return view('recharges.index', compact('recharges'));
    }

    public function create()
    {
        return view('recharges.create');
    }

    public function store(Request $request)
    {   

         $data = $request->validate([
            'employee_name'   => 'required',
            'mobile_number'      => 'required',
            'operator'      => 'required',
            'amount'      => 'required',
            'reference'      => 'nullable',
            'recharged_at'  => 'nullable|date',
            'status'         => 'required',
            'days'         => 'required',
            'notes'=> 'nullable'
        ]);

        
        // If status is completed and no recharged_at provided, set now
        if ($data['status'] === 'completed' && empty($data['recharged_at'])) {
            $data['recharged_at'] = Carbon::now();
        }

        // Ensure days is integer or null
        $data['days'] = isset($data['days']) ? (int) $data['days'] : null;

        $data['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $recharge = Recharge::create($data);

            DB::commit();

            return redirect()->route('recharges.index')
                ->with('success', 'Recharge created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            // Log the error if you have a logger, then return with error
            return back()->withInput()->withErrors(['error' => 'Failed to create recharge: ' . $e->getMessage()]);
        }
    }

    public function show(Recharge $recharge)
    {
        return view('recharges.show', compact('recharge'));
    }

    public function edit(Recharge $recharge)
    {
        return view('recharges.edit', compact('recharge'));
    }

    public function update(Request $request, Recharge $recharge)
    {
        $data = $request->validate([
            'employee_name'   => 'required',
            'mobile_number'      => 'required',
            'operator'      => 'required',
            'amount'      => 'required',
            'reference'      => 'nullable',
            'recharged_at'  => 'nullable|date',
            'status'         => 'required',
            'days'         => 'required',
            'notes'=> 'nullable'
        ]);

        
        // If status is completed and no recharged_at provided, set now
        if ($data['status'] === 'completed' && empty($data['recharged_at'])) {
            $data['recharged_at'] = Carbon::now();
        }

        // Ensure days is integer or null
        $data['days'] = isset($data['days']) ? (int) $data['days'] : null;

        $data['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $recharge->update($data);

            DB::commit();

            return redirect()->route('recharges.index')
                ->with('success', 'Recharge updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update recharge: ' . $e->getMessage()]);
        }
    }

    public function destroy(Recharge $recharge)
    {
        $recharge->delete();

        return redirect()->route('recharges.index')
            ->with('success', 'Recharge deleted.');
    }

    // optional helper: change status via quick action
    public function setStatus(Request $request, Recharge $recharge)
    {
        $request->validate(['status' => 'required|in:pending,completed,failed,refunded']);

        $status = $request->input('status');

        $updateData = ['status' => $status];

        if ($status === 'completed' && empty($recharge->recharged_at)) {
            $updateData['recharged_at'] = Carbon::now();
        } elseif ($status !== 'completed') {
            // do not automatically clear recharged_at on status change unless desired
        }

        $recharge->update($updateData);

        return back()->with('success', 'Status updated.');
    }
}