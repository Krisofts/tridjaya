<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmLostReason;
use App\CRM\Models\CrmPipeline;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LostReasonController extends Controller
{
    // -------------------------------------------------------------------------
    // INDEX
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $pipelineId = $request->get('pipeline_id');

        $reasons = CrmLostReason::with('pipeline')
            ->when($pipelineId, fn ($q) => $q->where('pipeline_id', $pipelineId))
            ->ordered()
            ->paginate(20);

        $pipelines = CrmPipeline::active()->orderBy('name')->get();

        return view('pages.crm.lost-reasons.index', compact('reasons', 'pipelines', 'pipelineId'));
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(): View
    {
        $pipelines = CrmPipeline::active()->orderBy('name')->get();

        return view('pages.crm.lost-reasons.create', compact('pipelines'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pipeline_id' => ['nullable', 'exists:crm_pipelines,id'],
            'name'        => ['required', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_default'  => ['boolean'],
            'is_active'   => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $slug = Str::slug($data['name']);

        // Cek duplikat slug dalam pipeline yang sama
        $exists = CrmLostReason::where('pipeline_id', $data['pipeline_id'] ?? null)
            ->where('slug', $slug)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Nama ini sudah ada untuk pipeline tersebut.'])->withInput();
        }

        CrmLostReason::create([
            'pipeline_id' => $data['pipeline_id'] ?? null,
            'name'        => $data['name'],
            'slug'        => $slug,
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_default'  => $data['is_default'] ?? false,
            'is_active'   => $data['is_active'] ?? true,
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('crm.lost-reasons.index')
            ->with('success', 'Alasan lost berhasil ditambahkan.');
    }

    // -------------------------------------------------------------------------
    // EDIT
    // -------------------------------------------------------------------------

    public function edit(CrmLostReason $lostReason): View
    {
        $pipelines = CrmPipeline::active()->orderBy('name')->get();

        return view('pages.crm.lost-reasons.edit', compact('lostReason', 'pipelines'));
    }

    public function update(Request $request, CrmLostReason $lostReason): RedirectResponse
    {
        $data = $request->validate([
            'pipeline_id' => ['nullable', 'exists:crm_pipelines,id'],
            'name'        => ['required', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_default'  => ['boolean'],
            'is_active'   => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $slug = Str::slug($data['name']);

        // Cek duplikat slug kecuali record ini sendiri
        $exists = CrmLostReason::where('pipeline_id', $data['pipeline_id'] ?? null)
            ->where('slug', $slug)
            ->where('id', '!=', $lostReason->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Nama ini sudah ada untuk pipeline tersebut.'])->withInput();
        }

        $lostReason->update([
            'pipeline_id' => $data['pipeline_id'] ?? null,
            'name'        => $data['name'],
            'slug'        => $slug,
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_default'  => $data['is_default'] ?? false,
            'is_active'   => $data['is_active'] ?? true,
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('crm.lost-reasons.index')
            ->with('success', 'Alasan lost berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // TOGGLE ACTIVE
    // -------------------------------------------------------------------------

    public function toggleActive(CrmLostReason $lostReason): RedirectResponse
    {
        $lostReason->update(['is_active' => ! $lostReason->is_active]);

        $status = $lostReason->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Alasan lost berhasil {$status}.");
    }

    // -------------------------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------------------------

    public function destroy(CrmLostReason $lostReason): RedirectResponse
    {
        // Cek apakah sedang dipakai oleh lead
        if ($lostReason->pipeline()->exists() && $lostReason->leads()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus — alasan ini masih dipakai oleh lead.');
        }

        $lostReason->delete();

        return redirect()
            ->route('crm.lost-reasons.index')
            ->with('success', 'Alasan lost dihapus.');
    }
}