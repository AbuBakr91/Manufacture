<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TechnicalCards;

class TechnicalCardController extends Controller
{
    public function index()
    {
        return TechnicalCards::all();
    }
}
