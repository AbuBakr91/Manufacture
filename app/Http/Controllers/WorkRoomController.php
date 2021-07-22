<?php

namespace App\Http\Controllers;

use App\Models\WorkRoom;
use Illuminate\Http\Request;

class WorkRoomController extends Controller
{
    public function index()
    {
        return WorkRoom::all();
    }
}
