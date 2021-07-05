<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
class CategoryController extends Controller
{
    /**
     * @return mixed
     * возвращаем категории
     */
    public function index()
    {
        return Categories::orderBy('name')->get();
    }
}
