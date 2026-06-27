<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // -------------------------------------------------------------------------
    // INDEX — ambil notifikasi untuk dropdown navbar
    // -------------------------------------------------------------------------

    public function index(): JsonResponse
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($n) => [
                'id'        => $n->id,
                'type'      => $n->data['type']      ?? 'info',
                'title'     => $n->data['title']     ?? '-',
                'body'      => $n->data['body']       ?? null,
                'url'       => $n->data['url']        ?? null,
                'lead_name' => $n->data['lead_name']  ?? null,
                'due_at'    => isset($n->data['due_at'])
                    ? \Carbon\Carbon::parse($n->data['due_at'])->format('H:i')
                    : null,
                'due_date'  => isset($n->data['due_at'])
                    ? (\Carbon\Carbon::parse($n->data['due_at'])->isToday()
                        ? 'Hari ini'
                        : \Carbon\Carbon::parse($n->data['due_at'])->translatedFormat('d M'))
                    : null,
                'priority'  => $n->data['priority']  ?? null,
                'is_read'   => ! is_null($n->read_at),
                'created_at'=> $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'total'  => auth()->user()->unreadNotifications()->count(),
            'items'  => $notifications,
        ]);
    }

    // -------------------------------------------------------------------------
    // MARK AS READ — tandai satu notifikasi sudah dibaca
    // -------------------------------------------------------------------------

    public function markRead(Request $request, string $id): JsonResponse
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first()
            ?->markAsRead();

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------
    // MARK ALL AS READ
    // -------------------------------------------------------------------------

    public function markAllRead(): JsonResponse
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}