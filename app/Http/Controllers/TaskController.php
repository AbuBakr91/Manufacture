<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PerformingTasks;
use App\Models\TaskOrder;
use App\Models\WorkPaused;
use App\Models\WorkWaiting;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * @param $user_id
     * @return bool
     */
    public function taskStatusUser($user_id)
    {
        $isTaskUser = new PerformingTasks;

        $StartStatus = $isTaskUser->where('user_id', $user_id)
            ->where('count', null)
            ->where('finish', null)->count();

        $pausedStatus = DB::table('performing_tasks')
            ->Join('work_paused', 'performing_tasks.id', '=', 'work_paused.work_id')
            ->where('performing_tasks.user_id', $user_id)
            ->whereNull('work_paused.pause_finish')->count();

        $waitingStatus = DB::table('performing_tasks')
            ->Join('work_waiting', 'performing_tasks.id', '=', 'work_waiting.work_id')
            ->where('performing_tasks.user_id', $user_id)
            ->whereNull('work_waiting.waiting_finish')->count();

        return response()->json([
            "start" => $StartStatus,
            "paused" => $pausedStatus,
            "waiting" => $waitingStatus
        ]);
    }

    public function currentTask($user_id): \Illuminate\Support\Collection
    {
        $currentTask = DB::table('performing_tasks')
            ->leftJoin('task_orders', 'performing_tasks.task_id', '=', 'task_orders.id')
            ->leftJoin('technical_cards', 'task_orders.card_id', '=', 'technical_cards.id')
            ->select('performing_tasks.id', 'technical_cards.name', 'task_orders.user_count')
           ->where( 'performing_tasks.count', null)->get();

        return $currentTask ?  $currentTask :  false;
    }

    public function addPaused(Request $request)
    {
        if ($request->begin) {
            $paused = new WorkPaused;
            $paused->pause_begin = Carbon::now();
            $paused->work_id = $request->work_id;
            $paused->save();
        }

        if ($request->finish) {
            $paused = WorkPaused::where('work_id', $request->work_id)
                ->where('pause_finish', null);

            $paused->update(['pause_finish' => Carbon::now()]);

        }
    }


    public function addWaiting(Request $request)
    {
        if ($request->begin) {
            $waiting = new WorkWaiting;
            $waiting->waiting_begin = Carbon::now();
            $waiting->work_id = $request->work_id;
            $waiting->save();
        }

        if ($request->finish) {
            $waiting = WorkWaiting::where('work_id', $request->work_id)
                ->where('waiting_finish', null);

            $waiting->update(['waiting_finish' => Carbon::now()]);

        }
    }
}


//SELECT tc.name, task_orders.user_count FROM performing_tasks pt
//LEFT JOIN task_orders
//ON pt.task_id = task_orders.id
//LEFT JOIN technical_cards tc
//ON tc.id = task_orders.card_id
