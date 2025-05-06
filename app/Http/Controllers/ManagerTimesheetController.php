<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManagerTimesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view-pending-approvals');

        $query = Timesheet::with(['user:id,name,email', 'project:id,project_name'])
            ->where('status', 'Submitted');

        $pendingEntries = $query->orderBy('date', 'asc') //najstariji datum prvo
                                 ->orderBy('user_id')
                                 ->paginate(15)
                                 ->withQueryString();

        return Inertia::render('web/timesheets/PendingApprovals', [
            'pagination' => $query,
            'pendingEntries' => $pendingEntries,
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
