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
    public function taskStatusUser($user_id)
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

    public function currentTask($user_id)
    {
        $currentTask = DB::table('')
    }
}
