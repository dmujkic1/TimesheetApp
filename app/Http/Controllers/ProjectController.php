<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Manager;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view-projects');

        $projects = Project::with('client')->paginate(10);
        return Inertia::render('web/projects/Index', [ 
            'projects' => $projects,
            'flashMessage'=>session('success')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create-project');
        $teams = Team::select('id','team_name')->get();
        $clients= Client::select('id', 'name')->get();
        return Inertia::render('web/projects/Create', [
            'teams' => $teams, 
            'clients' => $clients,
            'flashMessage'=> session('success')

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create-project');

        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:Active,Archived,Completed',
            'team_id' => 'required|array',
            'team_id.*' => 'exists:teams,id',
            'client_id' => 'required|exists:clients,id',
        ]);

        $project = Project::create([
            'project_name' => $validated['project_name'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
            'client_id' => $validated['client_id'],
        ]);

        $project->team()->sync($validated['team_id']); 

        session()->flash('success', 'Projekat je uspešno kreiran!');
        return redirect()->route('projects.index');

    }



    /**
     * Display the specified resource.
     */
    public function show(Project $projectId)
    {
        $this->authorize('view-projects');

        
        $project = $projectId->load(['team', 'client']);

        return Inertia::render('web/projects/Show', [
            'project' => $project,
            'teams' => $project->team,   
            'clients' => [$project->client], 
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($projectId)
{
    $this->authorize('edit-project');
    $project = Project::findOrFail($projectId);
    $project->load('team');
    $teams = Team::select('id', 'team_name')->get();
    $clients = Client::select('id', 'name')->get();
    return Inertia::render('web/projects/Edit', [
        'project' => $project,
        'teams' => $teams,
        'clients' => $clients,
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $projectId)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'required|string',
            'client_id' => 'required|exists:clients,id',  // Proverava da li klijent postoji
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:Active,Archived,Completed',
            'team_id' => 'required|array',  
            'team_id.*' => 'exists:teams,id', 
        ]);
        

        $project = Project::findOrFail($projectId);

        $project->update($validated);

        if (!empty($validated['team_id'])) {
            $project->team()->sync($validated['team_id']);
        }
        
        session()->flash('success', 'Projekat je uspešno ažuriran!');
        return Inertia::render('web/projects/Index', [
            'projects' => Project::with('client')->get(),
            'flashMessage' => session('success'),  
        ]);
    }


    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
                         ->with('success', 'Projekat uspješno izbrisan!');
    }

    public function archive(Project $project)
    {
        $project->update(['status' => 'Archived']);  

        return redirect()->route('projects.index');  
    }

}
