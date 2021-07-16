<?php

namespace App\Http\Controllers;

use App\Models\PerformingTasks;
use App\Models\TaskOrder;
use App\Models\TechCardTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\TechnicalCards;
use Illuminate\Support\Facades\DB;

class TechnicalCardController extends Controller
{

    public function index()
    {
        return DB::table('technical_cards')
            ->leftJoin('tech_card_times', 'tech_card_times.card_id', '=', 'technical_cards.id')
            ->leftJoin('categories', 'categories.id', '=', 'tech_card_times.card_id')
            ->select('technical_cards.id', 'technical_cards.name', 'technical_cards.cat_id', 'tech_card_times.dynamic_time', 'tech_card_times.statistical_time', 'categories.name as cat_name')
            ->get();
    }

    public function recordTimeTechCard()
    {
        $cards = $this->getCardsForTime();

        for($i=0; $i<count($cards); $i++) {
            if (($this->techCardTime($this->taskIds($cards[$i])) - $this->techCardWaited($this->taskIds($cards[$i])) - $this->techCardPaused($this->taskIds($cards[$i])) > 0)) {
                $techCard{$i} = new TechCardTime();
                $techCard{$i}->card_id = $cards[$i];
                $techCard{$i}->dynamic_time = round((
                        $this->techCardTime($this->taskIds($cards[$i])) -
                        $this->techCardWaited($this->taskIds($cards[$i])) -
                        $this->techCardPaused($this->taskIds($cards[$i])))/ $this->getCardAllCount($cards[$i]), 1);
                $techCard{$i}->save();
            } else {
                $techCard{$i} = new TechCardTime();
                $techCard{$i}->card_id = $cards[$i];
                $techCard{$i}->dynamic_time = 0;
                $techCard{$i}->save();
            }
        }

        return 'Данные записаны';
    }
    protected function getCardsForTime()
    {
        $cardId = DB::table('technical_cards')
            ->select('id')
            ->orderBy('id')
            ->get();

        $cards = [];

        foreach ($cardId as $card) {
            $cards[] = $card->id;
        }

        return $cards;
    }

    protected function taskIds($card_id): array
    {
        $tasks = DB::table('task_orders')
            ->join('performing_tasks', 'task_orders.id', '=', 'performing_tasks.task_id')
            ->select('performing_tasks.id')
            ->where('task_orders.card_id', $card_id)
            ->get();

        $result = [];

        foreach ($tasks as $task) {
            $result[] = $task->id;
        }
        return $result;
    }

    protected function techCardTime($array_tasks_id)
    {
        $workTime = [];

        foreach ($array_tasks_id as $task_id) {
            $workTime[] = PerformingTasks::where('id', $task_id)->get();
        }
        $seconds = 0;

        for ($i=0; $i<count($workTime); $i++) {
            foreach ($workTime[$i] as $time) {
                $begin = Carbon::createMidnightDate($time->begin);
                $finish = Carbon::createMidnightDate($time->finish);

                $seconds+= $begin->diffInSeconds($finish);
            }
        }

        return $seconds;
    }

    protected function getCardAllCount($card_id)
    {
        return DB::select('SELECT SUM(pt.count) + SUM(pt.defects) as count FROM performing_tasks pt
                    LEFT JOIN task_orders ts
                    ON ts.id = pt.task_id
                    WHERE ts.card_id = ?', array($card_id))[0]->count;
    }

    protected function techCardPaused($array_tasks_id)
    {
        $paused = [];

        foreach ($array_tasks_id as $task_id) {
            $paused[] = PerformingTasks::find($task_id)->taskPaused()->get();
        }

       $init = 0;

        for ($i=0; $i<count($paused); $i++) {
            foreach ($paused[$i] as $time) {
                $begin = Carbon::createMidnightDate($time->pause_begin);
                $finish = Carbon::createMidnightDate($time->pause_finish);

                $init += $begin->diffInSeconds($finish);
            }
        }

        return $init;
    }

    protected function techCardWaited($array_tasks_id)
    {
        $waiting = [];

        foreach ($array_tasks_id as $task_id) {
            $waiting[] = PerformingTasks::find($task_id)->taskWaiting()->get();
        }

        $init = 0;

        for ($i=0; $i<count($waiting); $i++) {
            foreach ($waiting[$i] as $time) {
                $begin = Carbon::createMidnightDate($time->waiting_begin);
                $finish = Carbon::createMidnightDate($time->waiting_finish);

                $init += $begin->diffInSeconds($finish);
            }
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

    public function recordStaticTime(Request $request)
    {
        $techCard = TechCardTime::where('card_id', $request->id);
        $techCard->update(["statistical_time" => $request->time]);

        return response()->json(["status" =>200]);
    }
}
