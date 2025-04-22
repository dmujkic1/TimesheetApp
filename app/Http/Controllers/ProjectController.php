<?php

namespace App\Http\Controllers;

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

        $projects = Project::all();
        return Inertia::render('web/projects/Index', [ 
            'projects' => $projects
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create-project');
        return Inertia::render('web/projects/Create');
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
            'client_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:Active,Archived,Completed',
            'team_id' => 'required|exists:teams,id',
        ]);

        $project= Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Projekt kreiran!');

        
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
        return Inertia::render('web/projects/Edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update-project');

        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'required|string',
            'client_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:Active,Archived,Completed',
            'team_id' => 'required|exists:teams,id',
        ]);

        $project->update($validated);  // Ažurira projekat sa novim podacima

        return redirect()->route('projects.index');  // Vraća na listu projekata
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
