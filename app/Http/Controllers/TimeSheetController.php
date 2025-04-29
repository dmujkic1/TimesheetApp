<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TimeSheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TimeSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view-timesheets');
        $currentUser = Auth::user();
        
        //idi na project, na njegovu team relaciju a zatim na svaki team idi na employee relaciju i primijeni uslov na Employee
        $projects = Project::whereHas('team.employee', function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id);
        })->with('team')->get();

        return Inertia::render('web/timesheets/Index', [
            'projects' => $projects,
        ]);
    }

    

    public function entries(Request $request)
    {
        $this->authorize('view-timesheets');
        $currentUser = Auth::user();
        $date = $request->date;

        $entries = TimeSheet::where('user_id', $currentUser->id)
            ->whereDate('date', $date)
            ->with('project')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'project' => $entry->project->project_name,
                    'start_time' => $entry->start_time,
                    'end_time' => $entry->end_time,
                    'break_start' => $entry->break_start ? $entry->break_start : null,
                    'break_end' => $entry->break_end ? $entry->break_end : null,
                    'notes' => $entry->notes,
                ];
            });

        return Inertia::render('web/timesheets/Index', [
            'entries' => $entries,
            'selectedDate' => $date,
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
        $this->authorize('create-timesheet');
        $currentUser = Auth::user();
        $date = $request->input('date'); //2025-04-29
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'project_id' => 'required|exists:projects,id',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'break_start' => 'nullable',
            'break_end' => 'nullable|after:break_start',
            'notes' => 'nullable|string|max:500'
        ]);

        //unosi tog dana
        $existingEntries = TimeSheet::where('user_id', $currentUser->id)
            ->whereDate('date', $validated['date'])
            ->get();

        foreach ($existingEntries as $entry) {
            if (($validated['start_time'] < $entry->end_time) && ($validated['end_time'] > $entry->start_time)) 
            {
                return back()->withErrors(['error' => 'Unos vremena se preklapa s postojećim unosom.']);
            }
        }

        Timesheet::create([
            'user_id' => $currentUser->id,
            'project_id' => $validated['project_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'break_start' => $validated['break_start'] ? $validated['break_start']  : null,
            'break_end' => $validated['break_end'] ? $validated['break_end']  : null,
            'notes' => $validated['notes'] ?? null,
            'date' => $validated['date'],
        ]);

        return redirect()->back()->with('success', 'Timesheet spašen!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeSheet $timeSheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeSheet $timeSheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeSheet $timeSheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeSheet $timeSheet)
    {
        //
    }
}
