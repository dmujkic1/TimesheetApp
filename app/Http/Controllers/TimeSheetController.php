<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as FacadesLog;
use Inertia\Inertia;

class TimesheetController extends Controller
{
    public function dailyWorkSummary()
    {
        $this->authorize('view-timesheets');
        $entries = Timesheet::where('user_id', Auth::user()->id)
            ->get()
            ->groupBy(function($entry) {
                return \Carbon\Carbon::parse($entry->date)->format('Y-m-d');
            });

        $summary = [];

        foreach ($entries as $date => $dailyEntries) {
            $totalMinutes = 0;

            foreach ($dailyEntries as $entry) {
                $start = \Carbon\Carbon::parse($entry->start_time);
                $end = \Carbon\Carbon::parse($entry->end_time);
                $breakStart = $entry->break_start ? \Carbon\Carbon::parse($entry->break_start) : null;
                $breakEnd = $entry->break_end ? \Carbon\Carbon::parse($entry->break_end) : null;
                $totalMinutes += $end->diffInMinutes($start);
                if ($breakStart && $breakEnd) {
                    $totalMinutes -= $breakEnd->diffInMinutes($breakStart);
                }
            }

            $hours = floor(abs($totalMinutes) / 60);
            $minutes = abs($totalMinutes) % 60;
            $summary[$date] = "{$hours}h{$minutes}min";
        }
        
        return response()->json($summary);
    }



    private function getUserProjects($user)
    {
        $projects = [];
        foreach ($user->employee->team as $team) {
            $projects[] = $team->project;
        }
        return $projects;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view-timesheets');
        $currentUser = Auth::user();
        $teams = $currentUser->employee->team;
        $projects = $this->getUserProjects($currentUser);

        //dd($projects);
        
        //idi na project, na njegovu team relaciju a zatim na svaki team idi na employee relaciju i primijeni uslov na Employee
        /* $projects = Project::whereHas('team.employee', function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id);
        })->with('team')->get(); */

        return Inertia::render('web/timesheets/Index', [
            'projects' => $projects,
        ]);
    }

    

    public function entries(Request $request)
    {
        $this->authorize('view-timesheets');
        $currentUser = Auth::user();
        $date = $request->date;

        $entries = Timesheet::where('user_id', $currentUser->id)
            ->whereDate('date', $date)
            ->with('project')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'project' => $entry->project->project_name,
                    'project_id' => $entry->project->id,
                    'start_time' => $entry->start_time,
                    'end_time' => $entry->end_time,
                    'break_start' => $entry->break_start ? $entry->break_start : null,
                    'break_end' => $entry->break_end ? $entry->break_end : null,
                    'notes' => $entry->notes,
                ];
            });

        $projects = $this->getUserProjects($currentUser);
        return Inertia::render('web/timesheets/Index', [
            'entries' => $entries,
            'selectedDate' => $date,
            'projects' => $projects,
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
            'notes' => 'nullable|string|max:500|min:4'
        ],
        [
            'date.required' => 'Datum je obavezan.',
            'project_id.required' => 'Projekt je obavezan.',
            'start_time.required' => 'Vrijeme početka rada je obavezno.',
            'end_time.required' => 'Vrijeme završetka rada je obavezno.',
            'end_time.after' => 'Vrijeme završetka mora biti nakon vremena početka.',
            'break_start.after' => 'Vrijeme početka pauze mora biti nakon vremena početka.',
            'break_end.after' => 'Vrijeme završetka pauze mora biti nakon vremena početka pauze.',
            'notes.max' => 'Napomena može sadržavati najviše 500 znakova.',
            'notes.min' => 'Napomena mora sadržavati najmanje 4 znaka.',
        ]);

        //za validaciju vremena oko preklapanja vise projekata odjednom
        $newEntryStart = Carbon::parse($validated['start_time']);
        $newEntryEnd = Carbon::parse($validated['end_time']);
        $newEntryBreakStart = $validated['break_start'] ? Carbon::parse($validated['break_start']) : null;
        $newEntryBreakEnd = $validated['break_end'] ? Carbon::parse($validated['break_end']) : null;

        //unosi tog dana
        $existingEntries = Timesheet::where('user_id', $currentUser->id)
            ->whereDate('date', $validated['date'])
            ->get();

        foreach ($existingEntries as $entry) {
            $existingEntryStart = $entry->start_time;
            $existingEntryEnd = $entry->end_time;
   
            // Check for overlap: (StartA < EndB) AND (EndA > StartB)
            if ($newEntryStart < $existingEntryEnd && $newEntryEnd > $existingEntryStart)
            {
                return back()->withErrors(['overlap_error' => 'Nije moguće dodati unos. Vrijeme rada se preklapa s postojećim unosom.']);
            }
   
            // Check if the new entry's break overlaps any existing work time
            if ($newEntryBreakStart && $newEntryBreakEnd) {
                if ($newEntryBreakStart < $existingEntryEnd && $newEntryBreakEnd > $existingEntryStart) {
                    return back()->withErrors(['break_error' => 'Pauza se preklapa s postojećim radnim vremenom.']);
                }
            }
        }
   
        // Check to ensure new break is within new work hours
        if ($newEntryBreakStart && $newEntryBreakEnd) {
            if ($newEntryBreakStart < $newEntryStart || $newEntryBreakEnd > $newEntryEnd) {
                return back()->withErrors(['break_error' => 'Pauza mora biti unutar radnog vremena.']);
            }
        }

        Timesheet::create([
            'user_id' => $currentUser->id,
            'project_id' => $validated['project_id'],
            'start_time' => $newEntryStart,
            'end_time' => $newEntryEnd,
            'break_start' => $newEntryBreakStart,
            'break_end' => $newEntryBreakEnd,
            'notes' => $validated['notes'] ?? null,
            'date' => $validated['date'],
        ]);

        //return redirect()->back()->with('success', 'Timesheet spašen!');
        return redirect()->route('timesheets.index')->with('success', 'Radni unos uspješno dodat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Timesheet $timesheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Timesheet $timesheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Timesheet $timesheet)
    {
        //$timesheet = Timesheet::find($timesheet);
        $this->authorize('edit-timesheet');
        //dd($timesheet->user_id, Auth::user()->id);
        //dd($timesheet['user_id']);
        if ($timesheet->user_id !== Auth::user()->id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'project_id' => 'required|exists:projects,id',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'break_start' => 'nullable',
            'break_end' => 'nullable|after:break_start',
            'notes' => 'nullable|string|max:500|min:4'
        ],[
            'date.required' => 'Datum je obavezan.',
            'project_id.required' => 'Projekt je obavezan.',
            'start_time.required' => 'Vrijeme početka rada je obavezno.',
            'end_time.required' => 'Vrijeme završetka rada je obavezno.',
            'end_time.after' => 'Vrijeme završetka mora biti nakon vremena početka.',
            'break_start.after' => 'Vrijeme početka pauze mora biti nakon vremena početka.',
            'break_end.after' => 'Vrijeme završetka pauze mora biti nakon vremena početka pauze.',
            'notes.max' => 'Napomena može sadržavati najviše 500 znakova.',
            'notes.min' => 'Napomena mora sadržavati najmanje 4 znaka.', 
        ]);

        $timesheet->update([
            'date' => $validatedData['date'],
            'project_id' => $validatedData['project_id'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'break_start' => $validatedData['break_start'],
            'break_end' => $validatedData['break_end'],
            'notes' => $validatedData['notes'],
        ]);

        //return redirect()->back()->with('success', 'Unos uspješno ažuriran!');
        return redirect()->route('timesheets.index')->with('success', 'Unos uspješno ažuriran!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Timesheet $timesheet)
    {
        $this->authorize('delete-timesheet');
        if ($timesheet->user_id !== Auth::user()->id) {
            abort(403);
        }

        $timesheet->delete();
        //return redirect()->back()->with('success', 'Unos uspješno obrisan!');
        return redirect()->route('timesheets.index')->with('success', 'Unos uspješno obrisan!');
    }
}
