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

    /**
     * @param $user_id
     * @return \Illuminate\Support\Collection
     */
    public function currentTask($user_id): \Illuminate\Support\Collection
    {
        $currentTask = DB::table('performing_tasks')
            ->leftJoin('task_orders', 'performing_tasks.task_id', '=', 'task_orders.id')
            ->leftJoin('technical_cards', 'task_orders.card_id', '=', 'technical_cards.id')
            ->select('performing_tasks.id', 'technical_cards.name', 'task_orders.user_count')
           ->where( 'performing_tasks.count', null)->get();

        return $currentTask ?  $currentTask :  false;
    }

    /**
     * @param Request $request
     */
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

    /**
     * @param Request $request
     */
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

    /**
     * @return int
     */
    public function userTaskPaused($user_id)
    {
         $allPauses = PerformingTasks::find($user_id)->taskPaused()->get();

        $init = 0;

        foreach ($allPauses as $pause) {
           $begin = Carbon::createMidnightDate($pause->pause_begin);
           $finish = Carbon::createMidnightDate($pause->pause_finish);

           $init += $begin->diffInSeconds($finish);
         }

        if ($init >= 60) {
            $day = floor($init / 86400);
            $hours = floor(($init -($day*86400)) / 3600);
            $minutes = floor(($init / 60) % 60);
        }

        if ($hours > 0) {
            return $hours.' час '.$minutes.' минуты';
        }

        return $minutes.' минуты';
    }

    /**
     * @return int
     */
    public function userTaskWaiting($task_id): int
    {
        $allPauses = PerformingTasks::find($task_id)->taskWaiting()->get();

        $init = 0;

        foreach ($allPauses as $pause) {
            $begin = Carbon::createMidnightDate($pause->waiting_begin);
            $finish = Carbon::createMidnightDate($pause->waiting_finish);

            $init += $begin->diffInMinutes($finish);
        }

        if ($init >= 60) {
            $day = floor($init / 86400);
            $hours = floor(($init -($day*86400)) / 3600);
            $minutes = floor(($init / 60) % 60);
        }

        if ($hours > 0) {
            return $hours.' час '.$minutes.' минуты';
        }

        return $minutes.' минут';
    }

    public function adminJournal()
    {
        $taskOrder = TaskOrder::all();

        $result = [];

        foreach ($taskOrder as $item) {
            $tasks[] = TaskOrder::find($item->id)->userTask()->get();

        }

        foreach ($tasks[0] as $task) {
            $taskPaused[] = $this->userTaskPaused($task->id);
        }

        $result['user_task'] = $tasks;

        return response()->json($taskPaused);
    }


    public function technicalOperation(): \Illuminate\Support\Collection
    {
        return DB::table('performing_tasks')
            ->join('users', 'users.id', '=', 'performing_tasks.user_id')
            ->join('task_orders', 'task_orders.id', '=', 'performing_tasks.task_id')
            ->join('technical_cards', 'technical_cards.id', '=', 'task_orders.card_id')
            ->select('performing_tasks.count', 'performing_tasks.defects', 'users.firstname', 'users.lastname', 'technical_cards.name')->get();
    }
}

