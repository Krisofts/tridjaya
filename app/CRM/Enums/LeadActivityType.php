<?php

namespace App\CRM\Enums;

enum LeadActivityType: string
{
    /*
    |--------------------------------------------------------------------------
    | LEAD CORE
    |--------------------------------------------------------------------------
    */

    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';

    /*
    |--------------------------------------------------------------------------
    | LEAD FIELDS
    |--------------------------------------------------------------------------
    */

    case STATUS_CHANGED = 'status_changed';
    case SOURCE_CHANGED = 'source_changed';
    case INTEREST_CHANGED = 'interest_changed';

    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    case ASSIGNED = 'assigned';
    case UNASSIGNED = 'unassigned';

    /*
    |--------------------------------------------------------------------------
    | COMMUNICATION
    |--------------------------------------------------------------------------
    */

    case CALL = 'call';
    case WHATSAPP = 'whatsapp';
    case MEETING = 'meeting';

    /*
    |--------------------------------------------------------------------------
    | NOTES
    |--------------------------------------------------------------------------
    */

    case NOTE = 'note';
    case NOTE_CHANGED = 'note_changed';

    /*
    |--------------------------------------------------------------------------
    | TASKS
    |--------------------------------------------------------------------------
    */

    case TASK_CREATED = 'task_created';
    case TASK_UPDATED = 'task_updated';
    case TASK_STATUS_CHANGED = 'task_status_changed';
    case TASK_ASSIGNED = 'task_assigned';
    case TASK_DUE_DATE_CHANGED = 'task_due_date_changed';
    case TASK_COMPLETED = 'task_completed';
    case TASK_DELETED = 'task_deleted';

    /*
    |--------------------------------------------------------------------------
    | REMINDERS
    |--------------------------------------------------------------------------
    */

    case REMINDER_CREATED = 'reminder_created';
    case REMINDER_UPDATED = 'reminder_updated';
    case REMINDER_STATUS_CHANGED = 'reminder_status_changed';
    case REMINDER_ASSIGNED = 'reminder_assigned';
    case REMINDER_RESCHEDULED = 'reminder_rescheduled';
    case REMINDER_DELETED = 'reminder_deleted';

    /*
    |--------------------------------------------------------------------------
    | GENERIC
    |--------------------------------------------------------------------------
    */

    case CUSTOM = 'custom';

    /*
    |--------------------------------------------------------------------------
    | LABELS (UI DISPLAY)
    |--------------------------------------------------------------------------
    */

    public function label(): string
    {
        return match ($this) {

            /*
            | LEAD CORE
            */
            self::CREATED => 'Lead dibuat',
            self::UPDATED => 'Lead diperbarui',
            self::DELETED => 'Lead dihapus',

            /*
            | FIELDS
            */
            self::STATUS_CHANGED => 'Status diubah',
            self::SOURCE_CHANGED => 'Sumber diubah',
            self::INTEREST_CHANGED => 'Interest diubah',

            /*
            | ASSIGNMENT
            */
            self::ASSIGNED => 'Lead ditugaskan',
            self::UNASSIGNED => 'Penugasan dihapus',

            /*
            | COMMUNICATION
            */
            self::CALL => 'Telepon',
            self::WHATSAPP => 'WhatsApp',
            self::MEETING => 'Meeting',

            /*
            | NOTES
            */
            self::NOTE => 'Catatan',
            self::NOTE_CHANGED => 'Catatan diperbarui',

            /*
            | TASKS
            */
            self::TASK_CREATED => 'Task dibuat',
            self::TASK_UPDATED => 'Task diperbarui',
            self::TASK_STATUS_CHANGED => 'Status task diubah',
            self::TASK_ASSIGNED => 'Task ditugaskan',
            self::TASK_DUE_DATE_CHANGED => 'Deadline task diubah',
            self::TASK_COMPLETED => 'Task selesai',
            self::TASK_DELETED => 'Task dihapus',

            /*
            | REMINDERS
            */
            self::REMINDER_CREATED => 'Reminder dibuat',
            self::REMINDER_UPDATED => 'Reminder diperbarui',
            self::REMINDER_STATUS_CHANGED => 'Status reminder diubah',
            self::REMINDER_ASSIGNED => 'Reminder ditugaskan',
            self::REMINDER_RESCHEDULED => 'Reminder dijadwalkan ulang',
            self::REMINDER_DELETED => 'Reminder dihapus',

            /*
            | GENERIC
            */
            self::CUSTOM => 'Aktivitas khusus',
        };
    }
}