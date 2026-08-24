<?php

namespace App\Http\Controllers;

use App\Models\Placement;

class PlacementFrontendController extends Controller
{
    public function index()
    {
        $placements = Placement::with([
            'college',
            'companyRelation',
            'course',
            'images',
        ])
        ->latest('placement_date')
        ->paginate(12);

        return view('placement_front', compact('placements'));
    }
}