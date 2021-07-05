<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TechnicalCards;

class CardController extends Controller
{
    /**
     * возвращем тех карты
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TechnicalCards::all();
    }

    /**
     * @param $id
     * @return mixed
     * возвращаем тех карты для переданной категории
     */
    public function getCardByCategory($id)
    {
        return TechnicalCards::orderBy('name')->where('cat_id', $id)->get();
    }
}
