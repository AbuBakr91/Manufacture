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
     * возвращаем состояние кнопок "старт", "пауза", "ожидание"
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
     * возвращаем текущую задачу пользователя, которую запустил пользователь
     */
    public function currentTask($user_id): \Illuminate\Support\Collection
    {
        $currentTask = DB::table('performing_tasks')
            ->leftJoin('task_orders', 'performing_tasks.task_id', '=', 'task_orders.id')
            ->leftJoin('technical_cards', 'task_orders.card_id', '=', 'technical_cards.id')
            ->select('performing_tasks.id', 'technical_cards.name', 'task_orders.user_count')
            ->where( 'performing_tasks.count', null)
            ->where('performing_tasks.user_id', $user_id)->get();

        return $currentTask ?  $currentTask :  false;
    }

    /**
     * @param Request $request
     * метод добавление паузы
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
     * метод добавление ожидания
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
     * метод подсчета времени паузы
     */
    public function userTaskPaused($user_id)
    {
        $allPauses = PerformingTasks::find($user_id)->taskPaused()->get();

        $init = 0;

        foreach ($allPauses as $pause) {
            $begin = Carbon::createMidnightDate($pause->pause_begin);
            $finish = Carbon::createMidnightDate($pause->pause_finish);

            $init += $begin->diffInMinutes($finish);
        }

        return $init;
    }

    /**
     * @return int
     * метод подсчета времени ожидания
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

        return $init;
    }

    /**
     * @param $task_id
     * @return int
     *
     * метод подсчета времени на задачу
     */
    public function userWorkTime($task_id)
    {
        $workTime = PerformingTasks::where('id', $task_id)->get();

        $minutes = 0;

        foreach ($workTime as $time) {
            $begin = Carbon::createMidnightDate($time->begin);
            $finish = Carbon::createMidnightDate($time->finish);

            $minutes+= $begin->diffInMinutes($finish);
        }

        return $minutes;
    }

    /**
     * @param $task_id
     * @return array
     * формирует детали по исполнителям(их затраченное время, паузы, ожидания, количество)
     */
    protected function getUserDetail($task_id): array
    {
        return DB::select('SELECT performing_tasks.id, users.lastname, performing_tasks.count, technical_cards.name,
                                        CAST(ROUND(TIME_TO_SEC(timediff(performing_tasks.finish,performing_tasks.begin))/60) as SIGNED) as worktime,
                                        (SELECT CAST(SUM(ROUND(TIME_TO_SEC(timediff(w.pause_finish,w.pause_begin))/60)) as SIGNED) FROM work_paused w WHERE w.work_id = performing_tasks.id) as paused,
                                        (SELECT CAST(SUM(ROUND(TIME_TO_SEC(timediff(w.waiting_finish ,w.waiting_begin))/60)) as SIGNED) FROM work_waiting w WHERE w.work_id = performing_tasks.id) as waiting
                                        FROM performing_tasks
                                    LEFT JOIN users
                                        ON users.id = performing_tasks.user_id
                                    LEFT JOIN task_orders
                                        ON task_orders.id = performing_tasks.task_id
                                    LEFT JOIN technical_cards
                                        ON task_orders.card_id = technical_cards.id
                                WHERE task_orders.id = ?
                                ORDER BY id', array($task_id));
        }

    /**
     * @return array
     * вывод заданий в журнал
     */
    public function adminJournal()
    {
        $taskOrder = TaskOrder::all()->where('user_count', '=', 0);

        $result = [];

        foreach ($taskOrder as $item) {
            $task_id[] = $item->id;
            $dep[] = TaskOrder::find($item->id)->taskDeportment()->get();
            $task[] = PerformingTasks::where('task_id', $item->id)->get();
            $cards[] = TaskOrder::find($item->id)->taskCardName()->get();
            $count[] = DB::table('task_orders')->select('count')->where('id', $item->id)->get();
            $date[] = DB::table('task_orders')->select('created_at as date')->where('id', $item->id)->get();
        }

        foreach ($dep as $key => $item) {
            $result[$key] = $this->userWorkTime(
                $this->getUserDetail($task_id[$key]) ? $this->getUserDetail($task_id[$key])[0]->id : ''
            );

            $result[$key] = [
                'id' => $task_id[$key],
                'department' => $item[0]->name,
                'card' => $cards[$key][0]->name,
                'counts' => $count[$key][0]->count,
                'date' => $date[$key][0]->date,
                'usersDetail' => $this->getUserDetail($task_id[$key])
            ];

        }

        return $result;
    }


    /**
     * @return \Illuminate\Support\Collection
     * возвращает коллекцию непроведенных задач
     */
    public function technicalOperation()
    {
        return DB::table('performing_tasks')
        ->join('users', 'users.id', '=', 'performing_tasks.user_id')
        ->join('task_orders', 'task_orders.id', '=', 'performing_tasks.task_id')
        ->join('technical_cards', 'technical_cards.id', '=', 'task_orders.card_id')
        ->select('performing_tasks.id', 'performing_tasks.count', 'performing_tasks.defects', 'users.firstname', 'users.lastname', 'technical_cards.name', 'technical_cards.tech_id', 'performing_tasks.finish')
        ->where('performing_tasks.count', '!=', 0)
        ->where('performing_tasks.operation', '=', 0)->get();
    }


    /**
     * @param Request $request
     * обновления статуса при првоедении тех операции
     */
    public function taskOperationStatus(Request $request)
    {
        $task = PerformingTasks::where('id', $request->id);

        $task->update(['operation' => true]);
    }
}

