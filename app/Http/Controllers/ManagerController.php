<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view-managers');
        return Inertia::render('web/managers/Index', [ 
            'managers' => Manager::with('user')->get(),
            'teams'=>Team::with('manager')->get(),
            'employees'=>Employee::with('team')->get(),
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
