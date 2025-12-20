<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\State;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    // Return JSON list of districts for a given state id
    public function getByState($stateId)
    {
        $districts = District::where('state_id', $stateId)->orderBy('name')->get(['id','name']);
        return response()->json($districts);
    }
}
