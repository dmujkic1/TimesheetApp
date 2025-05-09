<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Prikazuje stranicu i podatke za izvještaj sati po korisniku.
     */
    public function hoursPerUser(Request $request)
    {
        $this->authorize('view-admin-reports');

        $validatedFilters = $request->validate([
            'month' => 'sometimes|required|date_format:Y-m',
            'project_id' => 'nullable|integer|exists:projects,id',
        ],[
            'month.date_format' => 'Format mjeseca nije ispravan (YYYY-MM).',
            'project_id.integer' => 'ID projekta mora biti broj.',
            'project_id.exists' => 'Odabrani projekat ne postoji.',
        ]);

        $selectedMonth = $validatedFilters['month'] ?? Carbon::now()->subMonthNoOverflow()->format('Y-m');
        $selectedProjectId = $validatedFilters['project_id'] ?? null;

        $reportData = $this->generateUserHoursData($selectedMonth, $selectedProjectId);
        $projects = Project::orderBy('project_name')->get(['id', 'project_name']);

        return Inertia::render('web/reports/HoursPerUser', [
            'reportData' => $reportData,
            'projects' => $projects,
            'filters' => [ 
                'month' => $selectedMonth,
                'project_id' => $selectedProjectId ? (int)$selectedProjectId : null,
            ],
        ]);
    }

    /**
     * Pomoćna metoda za generiranje podataka za izvještaj.
     */
    private function generateUserHoursData($monthYear, $projectId = null)
{
    $startOfMonth = Carbon::parse($monthYear)->startOfMonth();
    $endOfMonth = Carbon::parse($monthYear)->endOfMonth();

    $query = Timesheet::with(['user.employee.team', 'project'])
        ->where('status', 'Approved')
        ->whereBetween('date', [$startOfMonth, $endOfMonth]);

    if ($projectId) {
        /* $query->whereHas('user.employee.team.project', function ($q) use ($projectId) {
            $q->where('project.id', $projectId);
        }); */
        $query->where('project_id', $projectId);
    }

    $timesheets = $query->get();

    $grouped = $timesheets->groupBy('user_id');

    $report = $grouped->map(function ($userTimesheets, $userId) {
        $user = $userTimesheets->first()->user;

        $totalWorkSeconds = 0;
        $totalBreakSeconds = 0;

        foreach ($userTimesheets as $entry) {
            $start = Carbon::parse($entry->start_time);
            $end = Carbon::parse($entry->end_time);
            $duration = $end->diffInSeconds($start);

            $breakSeconds = 0;
            if ($entry->break_start && $entry->break_end) {
                $breakStart = Carbon::parse($entry->break_start);
                $breakEnd = Carbon::parse($entry->break_end);
                $breakSeconds = $breakEnd->diffInSeconds($breakStart);
            }

            $totalWorkSeconds += ($duration - $breakSeconds);
            $totalBreakSeconds += $breakSeconds;
        }

        $netWorkSeconds = abs($totalWorkSeconds);
        $hours = floor($netWorkSeconds / 3600);
        $minutes = floor(($netWorkSeconds % 3600) / 60);

        /* $projectName = $user->employee->team && $user->employee->team->project->isNotEmpty()
        ? $user->employee->team->project->pluck('project_name')->join(', ')
        : '-'; */
        $teams = $user->employee->team ?? collect();

        $projectNames = $teams->flatMap(function ($team) {
            return $team->project->pluck('project_name');
        })->unique()->join(', ');

        $projectName = $projectNames ?: '-';

        return [
            'user_id' => $userId,
            'user_name' => $user->name,
            'user_project' => $projectName,
            'total_hours_decimal' => round($netWorkSeconds / 3600, 2),
            'total_hours_formatted' => sprintf('%dh %02dm', $hours, $minutes),
        ];
    })->sortBy('user_name')->values();

    return $report;
}

}
