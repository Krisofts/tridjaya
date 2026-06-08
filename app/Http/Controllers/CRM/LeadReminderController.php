<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\CRM\Models\LeadReminder;
use App\CRM\Services\LeadReminderService;
use App\Http\Requests\CRM\StoreLeadReminderRequest;
use App\Http\Requests\CRM\UpdateLeadReminderRequest;
use App\User\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadReminderController extends Controller
{
    public function __construct(
        protected LeadReminderService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX (ADMIN DASHBOARD)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $reminders = $this->service->filterForAdmin($request);

        return view('crm.reminders.index', [
            'reminders' => $reminders,
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(\App\CRM\Models\Lead $lead): View
    {
        return view('crm.reminders.create', [
            'lead' => $lead,
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(StoreLeadReminderRequest $request, \App\CRM\Models\Lead $lead): RedirectResponse
    {
        $this->service->create([
            ...$request->validated(),
            'lead_id' => $lead->id,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Reminder berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(LeadReminder $reminder): View
    {
        return view('crm.reminders.show', [
            'reminder' => $reminder->load(['lead', 'assignedTo', 'createdBy']),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(LeadReminder $reminder): View
    {
        return view('crm.reminders.edit', [
            'reminder' => $reminder,
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(UpdateLeadReminderRequest $request, LeadReminder $reminder): RedirectResponse
    {
        $this->service->update($reminder, $request->validated());

        return redirect()
            ->route('crm.reminders.show', $reminder)
            ->with('success', 'Reminder berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(LeadReminder $reminder): RedirectResponse
    {
        $lead = $reminder->lead;

        $reminder->delete();

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Reminder berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIONS
    |--------------------------------------------------------------------------
    */
    public function done(LeadReminder $reminder): RedirectResponse
    {
        $this->service->markDone($reminder);

        return back()->with('success', 'Reminder selesai.');
    }

    public function cancel(LeadReminder $reminder): RedirectResponse
    {
        $this->service->cancel($reminder);

        return back()->with('success', 'Reminder dibatalkan.');
    }

    public function reopen(LeadReminder $reminder): RedirectResponse
    {
        $this->service->reopen($reminder);

        return back()->with('success', 'Reminder dibuka kembali.');
    }

    public function assign(Request $request, LeadReminder $reminder): RedirectResponse
    {
        $this->service->assign($reminder, (int) $request->assigned_to);

        return back()->with('success', 'Reminder di-assign.');
    }
}