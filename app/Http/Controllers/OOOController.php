<?php

namespace App\Http\Controllers;

use App\Models\OOO;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OOOController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view-pending-approvals');

        $query = OOO::with(['user:id,name,email'])
            ->where('status', 'Pending');

        $pendingEntries = $query
                                 //najstariji datum prvo
                                 ->orderBy('user_id')
                                 ->paginate(15)
                                 ->withQueryString();

        return Inertia::render('web/ooos/PendingApprovals', [
            'pagination' => $query->paginate(15),
            'pendingEntries' => $pendingEntries,
        ]);
    }

    public function approveOooEntry(Request $request, OOO $ooo)
    {
        $this->authorize('approve-ooos');
        
        if ($ooo->status !== 'Pending') {
            return redirect()->route('manager.ooos.pending')->with('error', 'Unos nije u statusu "Pending".');
        }
        
        $ooo->update([
            'status' => 'Approved',
            'rejection_reason' => null, //ako je prethodno bio odbijen, reset sada
        ]);
        
        // Notifikacija korisniku?
        
        return redirect()->route('manager.ooos.pending')->with('success', "Unos za korisnika {$ooo->user->name} na dan {$ooo->date} je odobren.");
    }
    
    public function rejectOooEntry(Request $request, OOO $ooo)
    {
        $this->authorize('reject-ooos');

        if ($ooo->status !== 'Pending') {
            return redirect()->route('manager.ooos.pending')->with('error', 'Unos nije u statusu "Pending".');
        }
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:4|max:500',
        ]);
        $ooo->update([
            'status' => 'Rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Notifikacija korisniku?

        return redirect()->route('manager.ooos.pending')->with('success', "Unos za korisnika {$ooo->user->name} na dan {$ooo->date} je odbijen.");
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create-ooo-request');
        $validated = $request->validate([
            'type' => ['required', Rule::in(OOO::TYPESENUM)],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $ulogovaniUser = User::where('id', Auth::user()->id)->first();
        $ulogovaniUser->ooo()->create([
            ...$validated,
            'status' => 'Pending'
        ]);

        return redirect()->route('timesheets.index')->with('success', 'OOO zahtjev uspješno poslan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(OOO $oOO)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OOO $oOO)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OOO $oOO)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OOO $oOO)
    {
        //
    }
}
