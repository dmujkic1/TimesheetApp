<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view-employees');
        //$employees = Employee::paginate(10);
        return Inertia::render('web/employees/Index', [ //Employee::select('id')->get(),
            'employees' => Employee::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create-employee');
        return Inertia::render('web/employees/Create'); //NAPRAVITI Create.vue za unos novog uposlenika
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create-employee');

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'job_title' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employeeId)
    {
        $this->authorize('view-employee');
        return Inertia::render('web/employees/Show', [
            'employee' => $employeeId,
        ]);
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employeeId)
    {
        $this->authorize('edit-employee');
        return Inertia::render('web/employees/Edit', [
            'employee' => $employeeId,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employeeId)
    {
        $this->authorize('update-employee');
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employeeId->id,
            'job_title' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
        ]);
        $employee = Employee::findOrFail($employeeId);
        $employee->update($data);
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($employeeId)
    {
        $this->authorize('delete-employee');
        $employee = Employee::findOrFail($employeeId);
        
        $employee->delete();
        
        /* return redirect()->route('employees.index')->with('success', 'Employee deleted.'); */
        //return response()->noContent();
    }
}
