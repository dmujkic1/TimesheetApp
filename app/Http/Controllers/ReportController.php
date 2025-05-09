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
use Illuminate\Support\Str;

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

            $hours = abs(floor($totalWorkSeconds / 3600));
            $minutes = abs(floor(($totalWorkSeconds % 3600) / 60));

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
                'total_hours_formatted' => sprintf('%dh %02dm', $hours, $minutes),
            ];
        })->sortBy('user_name')->values();

        return $report;
    }

    public function exportHoursPerUser(Request $request)
    {
        $this->authorize('export-admin-reports');

        $validatedFilters = $request->validate([
            'month' => 'sometimes|required|date_format:Y-m',
            'project_id' => 'nullable|integer|exists:projects,id',
        ]);

        $selectedMonth = $validatedFilters['month'] ?? Carbon::now()->subMonthNoOverflow()->format('Y-m');
        $selectedProjectId = $validatedFilters['project_id'] ?? null;

        $reportData = $this->generateUserHoursData($selectedMonth, $selectedProjectId);

        if ($reportData->isEmpty()) {
            return redirect()->back()->with('error', 'Nema podataka za izvoz za odabrane filtere.');
        }

        // --- Priprema za CSV ---
        $monthDisplay = Carbon::parse($selectedMonth)->format('Y_m'); // Format za ime datoteke
        $fileName = 'izvjestaj_sati_korisnika_' . $monthDisplay;
        if ($selectedProjectId) {
            $project = Project::find($selectedProjectId);
            if ($project) {
                 $fileName .= '_' . Str::slug($project->project_name);
            }
        }
        $fileName .= '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Korisnik ID', 'Korisnik Ime', 'Projekti', 'Ukupno Sati (Formatirano)'];

        // --- Generiranje CSV odgovora ---
        $callback = function() use ($reportData, $columns) {
            $file = fopen('php://output', 'w');
            // Dodaj UTF-8 BOM za bolju kompatibilnost s Excelom
            fwrite($file, "\xEF\xBB\xBF");
            // Zaglavlje
            fputcsv($file, $columns);

            // Podaci
            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row['user_id'],
                    $row['user_name'],
                    $row['user_project'],
                    $row['total_hours_formatted'],
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
