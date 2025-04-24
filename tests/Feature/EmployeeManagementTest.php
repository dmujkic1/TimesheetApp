<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Mockery;

class EmployeeManagementTest extends TestCase
{
    
    /** @test */
    public function admin_can_create_employee_without_database()
    {
        $adminUser = Mockery::mock(User::class);
        $adminUser->shouldReceive('hasRole')->with('admin')->andReturn(true);

        $employeeMock = Mockery::mock(Employee::class);
        $employeeMock->shouldReceive('create')
            ->once()
            ->with([
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'job_title' => 'Developer',
            ])
            ->andReturn(true);

        $canCreate = $adminUser->hasRole('admin') ? $employeeMock->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'job_title' => 'Developer',
        ]) : false;

        $this->assertTrue($canCreate);
    }

    /** @test */
    public function non_admin_cannot_create_employee_without_database()
    {
        $nonAdminUser = Mockery::mock(User::class);
        $nonAdminUser->shouldReceive('hasRole')->with('admin')->andReturn(false);

        $employeeMock = Mockery::mock(Employee::class);
        $employeeMock->shouldNotReceive('create');

        $canCreate = false;
        if ($nonAdminUser->hasRole('admin')) {
            $canCreate = $employeeMock->create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'job_title' => 'Developer',
            ]);
        }

        $this->assertFalse($canCreate);
    }



    /** @test */
    public function validation_fails_on_invalid_employee_data_without_database()
    {
        // Pretpostavimo da će validacija za `first_name` i `email` pasti
        $validationFails = [
            'first_name' => '',
            'email' => 'fail',
        ];

        $this->assertFalse($this->validateEmployeeData($validationFails));
    }

    /** @test */
    public function validation_passes_on_valid_employee_data_without_database()
    {
        $employeeMock = Mockery::mock(Employee::class);

        // Pretpostavimo da će validacija za `first_name` i `email` proći
        $validationFails = [
            'first_name' => 'Validni',
            'email' => 'valid.validovic@gmail.com',
        ];

        $this->assertTrue($this->validateEmployeeData($validationFails));
    }

    /** @test */
    public function adequate_admin_role_assignation()
    {
        $adminUser = Mockery::mock(User::class);
        $adminUser->shouldReceive('hasRole')->with('admin')->andReturn(true);

        $nonAdminUser = Mockery::mock(User::class);
        $nonAdminUser->shouldReceive('hasRole')->with('admin')->andReturn(false);

        $this->assertTrue($this->userHasAdminRole($adminUser));
        $this->assertFalse($this->userHasAdminRole($nonAdminUser));
    }


    private function validateEmployeeData($data)
    {
        if (empty($data['first_name']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    private function userHasAdminRole($user)
    {
        return $user->hasRole('admin');
    }

}