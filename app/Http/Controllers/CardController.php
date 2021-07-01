<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TechnicalCards;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TechnicalCards::all();
    }

    public function getCardByCategory($id)
    {
        return TechnicalCards::orderBy('name')->where('cat_id', $id)->get();
    }
}
