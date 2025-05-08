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
        $this->authorize('create-ooo-requests');
        $ulogovaniUser = User::where('id', Auth::user()->id)->first();
        $oooRequests = $ulogovaniUser->ooo()->get();

        return Inertia::render('web/timesheets/Index', [
            'oooRequests' => $oooRequests,
        ]);
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
