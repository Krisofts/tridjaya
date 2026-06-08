<?php

namespace App\CRM\Policies;

use App\User\Models\User;
use App\CRM\Models\Lead;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('leads.view');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can('leads.view');
    }

    public function create(User $user): bool
    {
        return $user->can('leads.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->can('leads.edit');
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('leads.delete');
    }

    public function assign(User $user, Lead $lead): bool
    {
        return $user->can('leads.assign');
    }

    public function followUp(User $user, Lead $lead): bool
    {
        return $user->can('leads.followup');
    }

    public function manageActivities(User $user, Lead $lead): bool
    {
        return $user->can('leads.activities');
    }
}