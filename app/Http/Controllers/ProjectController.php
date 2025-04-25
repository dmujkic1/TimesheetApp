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

        $projects = Project::with('client')->get();
        return Inertia::render('web/projects/Index', [ 
            'projects' => $projects,
            'flash'=>session('success')
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
            'clients' => $clients

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

        return redirect()->route('projects.index')->with('success', 'Projekat uspješno kreiran!');
    }



    /**
     * Display the specified resource.
     */
    public function show(Project $projectId)
    {
        $this->authorize('view-projects');
        return Inertia::render('web/projects/Show', [
            'project' => $projectId
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
        $teams = Team::all(); // Dohvati sve timove
        $clients = Client::all();
        return Inertia::render('web/projects/Edit', [
            'project' => $project,
            'teams' => $teams, 
            'clients' =>$clients
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
            'client_id' => 'nullable|exists:clients,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:Active,Archived,Completed',
            'team_id' => 'array',
            'team_id.*' => 'integer|exists:teams,id',
        ]);

        // Učitavanje projekta koji želimo ažurirati
        $project = Project::findOrFail($projectId); 

        // Ažuriranje podataka o projektu
        $project->update($validated);

        // Sinhronizacija timova sa projektom (dodavanje novih timova i uklanjanje neodabranih)
        $project->team()->sync($validated['team_id']);

        // Preusmjeravanje korisnika na listu projekata sa porukom o uspjehu
        return Inertia::location(route('projects.index'));

    }

    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
                         ->with('success', 'Project has been deleted successfully!');
    }

    public function archive(Project $project)
    {
        $project->update(['status' => 'Archived']);  // Ažurira status projekta na "Archived"

        return redirect()->route('projects.index');  // Vraća na listu projekata
    }

}
