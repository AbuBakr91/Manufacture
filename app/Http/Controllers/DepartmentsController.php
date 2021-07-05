<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentsController extends Controller
{
    /**
     * @return Department[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Department::all();
    }

}
