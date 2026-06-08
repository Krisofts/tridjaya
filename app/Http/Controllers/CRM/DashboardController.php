<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\CRM\Models\Lead;
use App\CRM\Models\LeadTransaction;
use App\CRM\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC METRICS
        |--------------------------------------------------------------------------
        */
        $totalLeads = Lead::count();
        $newLeads   = Lead::where('status', 'new')->count();
        $wonLeads   = Lead::where('status', 'won')->count();
        $lostLeads  = Lead::where('status', 'lost')->count();
        $dealLeads  = Lead::where('status', 'deal')->count();

        /*
        |--------------------------------------------------------------------------
        | REVENUE
        |--------------------------------------------------------------------------
        */
        $totalRevenue = LeadTransaction::where('status', 'completed')->sum('amount');
        $cashRevenue  = LeadTransaction::where('type', 'cash')->sum('amount');
        $creditRevenue = LeadTransaction::where('type', 'credit')->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */
        $totalCustomers = Customer::count();
        $vipCustomers   = Customer::where('type', 'vip')->count();

        /*
        |--------------------------------------------------------------------------
        | FUNNEL DATA
        |--------------------------------------------------------------------------
        */
        $funnel = [
            'leads' => $totalLeads,
            'deal' => $dealLeads,
            'won' => $wonLeads,
            'customers' => $totalCustomers,
        ];

        /*
        |--------------------------------------------------------------------------
        | CONVERSION RATE
        |--------------------------------------------------------------------------
        */
        $conversion = [
            'lead_to_deal' => $totalLeads > 0
                ? round(($dealLeads / $totalLeads) * 100, 2)
                : 0,

            'deal_to_won' => $dealLeads > 0
                ? round(($wonLeads / $dealLeads) * 100, 2)
                : 0,

            'lead_to_won' => $totalLeads > 0
                ? round(($wonLeads / $totalLeads) * 100, 2)
                : 0,

            'won_to_customer' => $wonLeads > 0
                ? round(($totalCustomers / $wonLeads) * 100, 2)
                : 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | LATEST DATA
        |--------------------------------------------------------------------------
        */
        $latestLeads = Lead::latest()->limit(5)->get();

        $latestTransactions = LeadTransaction::with('lead')
            ->latest()
            ->limit(5)
            ->get();

        $latestCustomers = Customer::latest()
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('crm.dashboard.index', [
            // KPI
            'totalLeads' => $totalLeads,
            'newLeads' => $newLeads,
            'wonLeads' => $wonLeads,
            'lostLeads' => $lostLeads,

            // Revenue
            'totalRevenue' => $totalRevenue,
            'cashRevenue' => $cashRevenue,
            'creditRevenue' => $creditRevenue,

            // Customer
            'totalCustomers' => $totalCustomers,
            'vipCustomers' => $vipCustomers,

            // Funnel + Conversion
            'funnel' => $funnel,
            'conversion' => $conversion,

            // Latest
            'latestLeads' => $latestLeads,
            'latestTransactions' => $latestTransactions,
            'latestCustomers' => $latestCustomers,
        ]);
    }
}