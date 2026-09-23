<?php

namespace App\Http\Controllers;

use App\Models\SafetyInspection;
use App\Models\Store;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SafetyCleanlinessController extends Controller
{
    public function index(Request $request)
    {
        $query = SafetyInspection::with(['store', 'inspector']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $inspections = $query->latest('inspection_date')->paginate(15)->withQueryString();
        $stores = Store::where('is_active', true)->get();

        return view('safety.index', compact('inspections', 'stores'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        return view('safety.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'inspection_date' => 'required|date',
            'category' => 'required|in:SECURITY,FIRE_SAFETY,CLEANLINESS,PEST_CONTROL',
            'score' => 'required|integer|min:0|max:100',
            'status' => 'required|in:PASS,ACTION_REQUIRED',
            'remarks' => 'nullable|string',
            'checklist' => 'nullable|array',
        ]);

        $inspection = SafetyInspection::create([
            'store_id' => $validated['store_id'],
            'inspection_date' => $validated['inspection_date'],
            'inspector_id' => Auth::id(),
            'category' => $validated['category'],
            'checklist_items' => $validated['checklist'] ?? [],
            'score' => $validated['score'],
            'status' => $validated['status'],
            'remarks' => $validated['remarks'],
        ]);

        AuditLogService::log(
            'RECORD_SAFETY_INSPECTION',
            'SafetyInspection',
            (string) $inspection->id,
            null,
            $inspection->toArray(),
            'Pemeriksaan Keselamatan & Kebersihan AM 6.7 direkodkan bagi ' . $inspection->category
        );

        return redirect()->route('safety.index')->with('success', 'Rekod pemeriksaan keselamatan dan kebersihan berjaya disimpan.');
    }

    public function show(SafetyInspection $safety)
    {
        $safety->load(['store', 'inspector']);
        return view('safety.show', compact('safety'));
    }
}
