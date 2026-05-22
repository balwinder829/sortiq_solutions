<?php

namespace App\Http\Controllers\Finance;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class FinanceTabsController extends Controller
{
    public function index(Request $request)
    {
        // AJAX tab request
        if ($request->ajax() || $request->get('ajax')) {

            switch ($request->get('tab')) {
                case 'recharges':
                    return view('finance.tabs.recharges_table');
                case 'office-assets':
                    return view('finance.tabs.office_assets_table');
                case 'travel-expenses':
                    return view('finance.tabs.travel_expenses_table');
                case 'event-expenses':
                    return view('finance.tabs.event_expenses_table');
                case 'office-accessories':
                    return view('finance.tabs.office_accessories_table');
                case 'office-cleaning':
                    return view('finance.tabs.office_cleaning_table');
                case 'tea-pantry':
                    return view('finance.tabs.tea_pantry_table');
                case 'pantry':
                    return view('finance.tabs.pantry_table');
                case 'office-paper':
                    return view('finance.tabs.office_paper_table');
                case 'electricity':
                default:
                    return view('finance.tabs.electricity_table');
            }
        }

        // Main page
        return view('finance.master');
    }
}