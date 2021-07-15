<?php

namespace App\Http\Controllers;

use App\Models\PerformingTasks;
use App\Models\TaskOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TechnicalCards;
use Illuminate\Support\Facades\DB;

class TechnicalCardController extends Controller
{

    public function index()
    {
        $cards = $this->getCardsForTime();

        return $this->techCardWaited(1);
    }

    protected function getCardsForTime(): array
    {
        $cardId = DB::table('task_orders')
            ->select('card_id')
            ->distinct()
            ->get();

        $cards = [];

        foreach ($cardId as $key => $card) {
            $cards[$key] = $this->taskIds($card->card_id);
        }


        return $cards;
    }

    protected function taskIds($card_id): \Illuminate\Support\Collection
    {
        return DB::table('task_orders')
            ->join('performing_tasks', 'task_orders.id', '=', 'performing_tasks.task_id')
            ->select('performing_tasks.id')
            ->where('task_orders.card_id', $card_id)
            ->get();
    }

    protected function techCardTime($card_id)
    {

    }

    protected function techCardPaused($task_id)
    {
       $paused = PerformingTasks::find($task_id)->taskPaused()->get();

       $init = 0;

        foreach ($paused as $pause) {
            $begin = Carbon::createMidnightDate($pause->pause_begin);
            $finish = Carbon::createMidnightDate($pause->pause_finish);

            $init += $begin->diffInSeconds($finish);
        }

        return $init;
    }

    protected function techCardWaited($task_id)
    {
        $waiting = PerformingTasks::find($task_id)->taskWaiting()->get();

        $init = 0;

        foreach ($waiting as $wait) {
            $begin = Carbon::createMidnightDate($wait->waiting_begin);
            $finish = Carbon::createMidnightDate($wait->waiting_finish);

            $init += $begin->diffInSeconds($finish);
        }

        return $init;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TechnicalCards::find($id);
    }
}
