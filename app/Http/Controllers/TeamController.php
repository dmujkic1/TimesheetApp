<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function assignForm()
    {
        return Inertia::render('web/teams/Assign', [
            'teams' => Team::all(),
            'employees' => Employee::all(),
        ]);
    }

    public function assignEmployees(Request $request)
    {
        $data = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $team = Team::find($data['team_id']);
        $team->employees()->sync($data['employee_ids']); // many-to-many

        return redirect()->route('teams.assign.form')->with('success', 'Zaposlenici uspješno dodijeljeni timu.');
    }
}
