<?php

namespace App\CRM\Services;

use App\CRM\Models\Lead;
use App\CRM\Models\LeadActivity;
use App\CRM\Enums\LeadActivityType;
use App\User\Models\User;
use Illuminate\Support\Facades\Auth;

class LeadActivityService
{
    /*
    |--------------------------------------------------------------------------
    | CORE CREATE
    |--------------------------------------------------------------------------
    */

    public function create(
        Lead $lead,
        LeadActivityType $type,
        string $description,
        ?string $title = null,
        ?int $createdBy = null,
    ): LeadActivity {
        return LeadActivity::create([
            'lead_id'     => $lead->id,
            'type'        => $type->value,
            'title'       => $title ?? $type->label(),
            'description' => $description,
            'created_by'  => $createdBy ?? Auth::id(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SYSTEM ACTIVITIES
    |--------------------------------------------------------------------------
    */

    public function leadCreated(Lead $lead): LeadActivity
    {
        return $this->create(
            $lead,
            LeadActivityType::CREATED,
            'Lead dibuat.',
        );
    }

    public function statusChanged(Lead $lead, ?string $oldStatus, string $newStatus): LeadActivity
    {
        $oldLabel = Lead::statuses()[$oldStatus] ?? $oldStatus ?? '-';
        $newLabel = Lead::statuses()[$newStatus] ?? $newStatus ?? '-';

        return $this->create(
            $lead,
            LeadActivityType::STATUS_CHANGED,
            "Status diubah dari '{$oldLabel}' ke '{$newLabel}'.",
        );
    }

    public function sourceChanged(Lead $lead, ?string $oldSource, ?string $newSource): LeadActivity
    {
        $oldLabel = Lead::sources()[$oldSource] ?? $oldSource ?? '-';
        $newLabel = Lead::sources()[$newSource] ?? $newSource ?? '-';

        return $this->create(
            $lead,
            LeadActivityType::SOURCE_CHANGED,
            "Sumber lead diubah dari '{$oldLabel}' menjadi '{$newLabel}'.",
        );
    }

    public function interestChanged(Lead $lead, ?string $oldInterest, ?string $newInterest): LeadActivity
    {
        $oldLabel = Lead::interests()[$oldInterest] ?? $oldInterest ?? '-';
        $newLabel = Lead::interests()[$newInterest] ?? $newInterest ?? '-';

        return $this->create(
            $lead,
            LeadActivityType::INTEREST_CHANGED,
            "Interest diubah dari '{$oldLabel}' menjadi '{$newLabel}'.",
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    public function assigned(Lead $lead, User $user): LeadActivity
    {
        return $this->create(
            $lead,
            LeadActivityType::ASSIGNED,
            "Lead ditugaskan ke {$user->name}.",
        );
    }

    public function unassigned(Lead $lead, User $user): LeadActivity
    {
        return $this->create(
            $lead,
            LeadActivityType::UNASSIGNED,
            "Penugasan {$user->name} dilepas.",
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NOTES
    |--------------------------------------------------------------------------
    */

    public function noteChanged(Lead $lead, ?string $oldNote, ?string $newNote): LeadActivity
    {
        if (blank($oldNote) && filled($newNote)) {
            $description = 'Catatan lead ditambahkan.';
        } elseif (filled($oldNote) && blank($newNote)) {
            $description = 'Catatan lead dihapus.';
        } else {
            $description = 'Catatan lead diperbarui.';
        }

        return $this->create(
            $lead,
            LeadActivityType::NOTE,
            $description,
            'Note Updated',
        );
    }

    public function note(Lead $lead, string $note): LeadActivity
    {
        return $this->create(
            $lead,
            LeadActivityType::NOTE,
            $note,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | COMMUNICATION
    |--------------------------------------------------------------------------
    */

    public function call(Lead $lead, string $description): LeadActivity
    {
        return $this->create(
            $lead,
            LeadActivityType::CALL,
            $description,
        );
    }

    public function whatsapp(Lead $lead, string $description): LeadActivity
    {
        return $this->create(
            $lead,
            LeadActivityType::WHATSAPP,
            $description,
        );
    }

    public function meeting(Lead $lead, string $description): LeadActivity
    {
        return $this->create(
            $lead,
            LeadActivityType::MEETING,
            $description,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOM
    |--------------------------------------------------------------------------
    */

    public function custom(Lead $lead, LeadActivityType|string $type, string $description): LeadActivity
    {
        return LeadActivity::create([
            'lead_id'     => $lead->id,
            'type'        => $type instanceof LeadActivityType ? $type->value : $type,
            'title'       => ucfirst($type instanceof LeadActivityType ? $type->value : $type),
            'description' => $description,
            'created_by'  => Auth::id(),
        ]);
    }
}