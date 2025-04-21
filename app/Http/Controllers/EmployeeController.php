<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view-employees');

        return Inertia::render('web/employees/Index', [
            'pagination' => Employee::paginate(10), //'flash' success i error automatski rade preko Inertia jer su u defineProps
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
            'first_name' => 'required|string|max:255|min:3|regex:/^[a-zA-ZčćžšđČĆŽŠĐ\s-]+$/',
            'last_name' => 'required|string|max:255|min:3|regex:/^[a-zA-ZčćžšđČĆŽŠĐ\s-]+$/', //ipak min staviti i ugasiti special characters
            'email' => 'required|email|unique:employees,email',
            'job_title' => 'required|string|max:255|min:5|regex:/^[a-zA-ZčćžšđČĆŽŠĐ\s-]+$/',
            'hire_date' => 'required|date',
            'status' => 'required|string|in:aktivan,neaktivan',
        ],[
            'first_name.required' => 'Unesite ime uposlenika.',
            'first_name.regex' => 'Ime može sadržavati samo slova i razmake.',
            'first_name.min' => 'Ime mora imati najmanje 3 karaktera.',
            'first_name.max' => 'Ime može imati najviše 255 karaktera.',
            'last_name.required' => 'Unesite prezime uposlenika.',
            'last_name.regex' => 'Prezime može sadržavati samo slova i razmake.',
            'last_name.min' => 'Prezime mora imati najmanje 3 karaktera.',
            'last_name.max' => 'Prezime može imati najviše 255 karaktera.',
            'email.email' => 'Unesite ispravnu email adresu.',
            'email.unique' => 'Email adresa već postoji.',
            'email.max' => 'Email adresa može imati najviše 255 karaktera.',
            'email.min' => 'Email adresa mora imati najmanje 5 karaktera.',
            'email.required' => 'Unesite email uposlenika.',
            'job_title.required' => 'Unesite zvanje uposlenika.',
            'job_title.min' => 'Zvanje mora imati najmanje 5 karaktera.',
            'job_title.max' => 'Zvanje može imati najviše 255 karaktera.',
            'job_title.regex' => 'Zvanje može sadržavati samo slova i razmake.',
            'hire_date.required' => 'Unesite datum zaposlenja uposlenika.',
            'status.required' => 'Unesite status uposlenika.',
            'status.in' => 'Status mora biti ili "aktivan" ili "neaktivan".',
        ]);
        $validated['status'] = $validated['status'] === 'aktivan';
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make("mojaSifra"),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $validated['user_id'] = $user->id;

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
    public function edit($employeeId)
    {
        $this->authorize('edit-employee');
        $employee = Employee::findOrFail($employeeId);
        return Inertia::render('web/employees/Edit', [
            'employee' => $employee,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $employeeId)
    {
        $this->authorize('update-employee');
        $employee = Employee::findOrFail($employeeId);

        $data = $request->validate([
            'first_name' => 'required|string|max:255|min:3|regex:/^[a-zA-ZčćžšđČĆŽŠĐ\s-]+$/',
            'last_name' => 'required|string|max:255|min:3|regex:/^[a-zA-ZčćžšđČĆŽŠĐ\s-]+$/', //ipak min staviti i ugasiti special characters
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'job_title' => 'required|string|max:255|min:5|regex:/^[a-zA-ZčćžšđČĆŽŠĐ\s-]+$/',
            'hire_date' => 'required|date',
            'status' => 'required|string|in:aktivan,neaktivan',
        ],[
            'first_name.required' => 'Unesite ime uposlenika.',
            'first_name.regex' => 'Ime može sadržavati samo slova i razmake.',
            'first_name.min' => 'Ime mora imati najmanje 3 karaktera.',
            'first_name.max' => 'Ime može imati najviše 255 karaktera.',
            'last_name.required' => 'Unesite prezime uposlenika.',
            'last_name.regex' => 'Prezime može sadržavati samo slova i razmake.',
            'last_name.min' => 'Prezime mora imati najmanje 3 karaktera.',
            'last_name.max' => 'Prezime može imati najviše 255 karaktera.',
            'email.email' => 'Unesite ispravnu email adresu.',
            'email.unique' => 'Email adresa već postoji.',
            'email.max' => 'Email adresa može imati najviše 255 karaktera.',
            'email.min' => 'Email adresa mora imati najmanje 5 karaktera.',
            'email.required' => 'Unesite email uposlenika.', //ERROR PORUKE NA NASEM
            'job_title.required' => 'Unesite zvanje uposlenika.',
            'job_title.min' => 'Zvanje mora imati najmanje 5 karaktera.',
            'job_title.max' => 'Zvanje može imati najviše 255 karaktera.',
            'job_title.regex' => 'Zvanje može sadržavati samo slova i razmake.',
            'hire_date.required' => 'Unesite datum zaposlenja uposlenika.',
            'status.required' => 'Unesite status uposlenika.',
            'status.in' => 'Status mora biti ili "aktivan" ili "neaktivan".',
        ]);
        $data['status'] = $data['status'] === 'aktivan';
        //$data['updated_at'] = now();
        $employee->update($data);

        $user = $employee->user;
        $user->name = $data['first_name'] . ' ' . $data['last_name'];
        $user->email = $data['email'];
        //$user->updated_at = now();
        $user->save();

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $this->authorize('delete-employee'); //niko ne moze admina i niko ne moze svoju rolu brisat
        $currentUser = Auth::user();
        //dd($userEmployee);
        //dd($employee->id);
        
        if ($employee->user->hasRole('admin'))
        return redirect()->route('employees.index')->with('error', 'You cannot delete an admin.');
        //return redirect()->back()->with('error', 'You cannot delete an admin.');

        else if ($employee->user->hasRole('manager') && $currentUser->hasRole('manager')) 
        return redirect()->back()->with('error', 'A manager cannot delete a manager.');

        $employee->update(['status' => false]);
        $employee->user->delete();
        $employee->delete();
        return redirect()->back()->with('success', 'Employee successfully deleted!');
        
        /* return redirect()->route('employees.index')->with('success', 'Employee deleted.'); */
        //return response()->noContent();
    }
}
