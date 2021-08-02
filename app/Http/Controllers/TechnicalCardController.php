<?php

namespace App\Http\Controllers;

use App\Http\Controllers\WebHooksController;
use App\Models\MaterialForCard;
use App\Models\Materials;
use App\Models\PerformingTasks;
use App\Models\Products;
use App\Models\TaskOrder;
use App\Models\TechCardTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\TechnicalCards;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use mysql_xdevapi\Exception;

class TechnicalCardController extends Controller
{
    /**
     * @var \App\Http\Controllers\WebHooksController
     */
    private $webhook;

    public function __construct(WebHooksController $webhook)
    {
        $this->webhook = $webhook;
    }

    protected function outputJson($card_id): \Illuminate\Support\Collection
    {
        return DB::table('material_for_cards')
                ->join('product_names', 'material_for_cards.product_id', '=', 'product_names.id')
                ->select('product_names.name', 'material_for_cards.count')
                ->where('material_for_cards.card_id', $card_id)
                ->distinct()
                ->get();
    }

    protected function getUpdate($card_id): \Illuminate\Support\Collection
    {
       return DB::table('materials')->select('updated_at')->where('card_id', $card_id)->get();
    }


    public function index()
    {
        $cards = DB::table('technical_cards')
            ->leftJoin('tech_card_times', 'tech_card_times.card_id', '=', 'technical_cards.id')
            ->leftJoin('categories', 'categories.id', '=', 'tech_card_times.card_id')
            ->select('technical_cards.id', 'technical_cards.tech_id', 'technical_cards.name', 'technical_cards.cat_id', 'tech_card_times.dynamic_time', 'tech_card_times.statistical_time', 'categories.name as cat_name')
            ->where('technical_cards.deleted', '=', 0)
            ->orderBy('technical_cards.name')
            ->get();

         foreach ($cards as $card) {
             $id[] = $card->id;
             $name[] = $card->name;
             $cat_id[] = $card->cat_id;
             $tech_id[] = $card->tech_id;
             $cat_name[] = $card->cat_name;
             $dynamic_time[] = $card->dynamic_time;
             $statistical_time[] = $card->statistical_time;
         }

         for ($i=0; $i<count($id); $i++) {
             $result[$i] = [
                 "id" => $id[$i],
                 "name" => $name[$i],
                 "cat_id" => $cat_id[$i],
                 "cat_name" => $cat_name[$i],
                 "dynamic_time" => $dynamic_time[$i],
                 "updated_at" => $this->getUpdate($tech_id[$i])[0]->updated_at,
                 "statistical_time" => $statistical_time[$i],
                 "materials" => $this->outputJson($id[$i])
            ];
         }
        return json_encode($result);
    }

    public function recordTimeTechCard()
    {
        date_default_timezone_set('Europe/Moscow');

        $cards = $this->getCardsForTime();

        for($i=0; $i<count($cards); $i++) {
            if (($this->techCardTime($this->taskIds($cards[$i])) - $this->techCardWaited($this->taskIds($cards[$i])) - $this->techCardPaused($this->taskIds($cards[$i])) > 0)) {
                $techCard{$i} = new TechCardTime();
                $techCard{$i}->card_id = $cards[$i];
                $techCard{$i}->dynamic_time = round((
                        $this->techCardTime($this->taskIds($cards[$i])) -
                        $this->techCardWaited($this->taskIds($cards[$i])) -
                        $this->techCardPaused($this->taskIds($cards[$i])))/ ($this->getCardAllCount($cards[$i]) ?? 1), 1);
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
        date_default_timezone_set('Europe/Moscow');
        try {
            $techCard = TechCardTime::where('card_id', $request->id);
            if ($request->static) {
                $techCard->update(["statistical_time" => (int)$request->time]);
            } else {
                $techCard->update(["dynamic_time" => (int)$request->time * 60]);
            }
            return response()->json(["status" =>200]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateTechCard($id)
    {
        $card_id = TechnicalCards::select('tech_id')->where('id', $id)->get()[0]->tech_id;
        $this->webhook->updateCardForId($card_id);

        return response()->json(["status" => true]);
    }

    public function updateAllTechCards()
    {

        $cardsId = TechnicalCards::select('tech_id')->get();
        $allProduct1 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product'))->rows;
        $allProduct2 = json_decode(Http::withBasicAuth(env('M_LOGIN'), env('M_PASS'))->get('https://online.moysklad.ru/api/remap/1.2/entity/product?offset=1000'))->rows;
        $allProducts[] = array_merge($allProduct1, $allProduct2);
        foreach ($cardsId as $card) {
            $this->webhook->updateTechCard($card->tech_id, $allProducts);
        }

        return response()->json(["status" => true]);

    }
}
