<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmNotification;
use App\CRM\Models\CrmTask;
use App\CRM\Services\NotificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $service,
    ) {}

    // -------------------------------------------------------------------------
    // AJAX — untuk bell icon di header
    // -------------------------------------------------------------------------

    /**
     * GET /crm/notifications/unread
     * Dipanggil via fetch() dari JS setiap 30 detik.
     */
    public function unread(): JsonResponse
    {
        $user  = Auth::user();
        $items = $this->service->unread($user, 10);
        $count = $this->service->unreadCount($user);

        return response()->json([
            'count' => $count,
            'items' => $items->map(fn ($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'action_url' => $n->action_url,
                'icon'       => $n->icon(),
                'color'      => $n->iconColor(),
                'created_at' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // INDEX — halaman semua notifikasi
    // -------------------------------------------------------------------------

    public function index(): View
    {
        $notifications = $this->service->all(Auth::user());

        return view('pages.crm.notifications.index', compact('notifications'));
    }

    // -------------------------------------------------------------------------
    // MARK READ
    // -------------------------------------------------------------------------

    public function markRead(CrmNotification $notification): RedirectResponse
    {
        $this->service->markRead($notification);

        if ($notification->action_url) {
            // action_url disimpan sebagai path (/crm/leads/1)
            // resolve ke URL absolut berdasarkan host saat ini
            $url = url($notification->action_url);
            return redirect($url);
        }

        return redirect()->route('crm.notifications.index');
    }

    public function markAllRead(): JsonResponse
    {
        $this->service->markAllRead(Auth::user());

        return response()->json(['success' => true]);
    }

    // -------------------------------------------------------------------------
    // CREATE — Manual reminder dari sales
    // -------------------------------------------------------------------------

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'message'   => ['nullable', 'string', 'max:500'],
            'lead_id'   => ['nullable', 'exists:crm_leads,id'],
            'task_id'   => ['nullable', 'exists:crm_tasks,id'],
            'remind_at' => ['nullable', 'date', 'after:now'],
        ]);

        $this->service->createManual(
            user     : Auth::user(),
            title    : $data['title'],
            message  : $data['message']  ?? null,
            leadId   : $data['lead_id']  ?? null,
            taskId   : $data['task_id']  ?? null,
            remindAt : isset($data['remind_at'])
                ? \Carbon\Carbon::parse($data['remind_at'])
                : null,
        );

        return back()->with('success', 'Reminder berhasil disimpan.');
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function destroy(CrmNotification $notification): JsonResponse
    {
        $this->service->delete($notification);

        return response()->json(['success' => true]);
    }

    public function destroyAllRead(): RedirectResponse
    {
        $this->service->deleteAllRead(Auth::user());

        return back()->with('success', 'Notifikasi yang sudah dibaca dihapus.');
    }
}