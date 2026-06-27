<?php

namespace App\CRM\Providers;

use App\CRM\ViewComposers\LeadComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class CrmServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer([
            'crm.leads.index',
            'crm.leads.create',
            'crm.leads.edit',
            'crm.leads.show',
            'crm.my-leads.index',
            'crm.my-leads.show',
            'crm.my-leads.tasks',
        ], LeadComposer::class);
    }
}