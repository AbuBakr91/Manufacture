<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformingTasks;
use App\Models\TaskOrder;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * @param $user_id
     * @return bool
     */
    public function taskStatusUser($user_id): bool
    {
        $isTaskUser = new PerformingTasks;

        $status = $isTaskUser->where('user_id', $user_id)
            ->where('count', null)
            ->where('finish', null)->count();

        if ($status) {
            return true;
        }

        return false;
    }

    public function currentTask($user_id): \Illuminate\Support\Collection
    {
       return $currentTask = DB::table('performing_tasks')
            ->leftJoin('task_orders', 'performing_tasks.task_id', '=', 'task_orders.id')
            ->leftJoin('technical_cards', 'task_orders.card_id', '=', 'technical_cards.id')
            ->select('technical_cards.name', 'task_orders.user_count')->get();
    }
}
//SELECT tc.name, task_orders.user_count FROM performing_tasks pt
//LEFT JOIN task_orders
//ON pt.task_id = task_orders.id
//LEFT JOIN technical_cards tc
//ON tc.id = task_orders.card_id
