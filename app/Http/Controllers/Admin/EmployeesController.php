<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Admin\Employees\StoreEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('user')->get()->map(function($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->user->name,
                'phone' => $employee->user->phone,
                'image_url' => $employee->user->image_url,
            ];
        });

        return $this->generalResponse($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        return $request->store();
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return $this->generalResponse($employee->load('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        return $request->update($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        $employee->load('user');
        $employee->user->delete();
        return $this->generalResponse(null, 'Deleted Successfully', 200);
    }
}
