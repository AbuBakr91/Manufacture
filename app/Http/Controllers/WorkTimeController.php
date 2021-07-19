<?php

namespace App\Http\Controllers;

use App\Models\TechCardTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PerformingTasks;
use App\Models\TaskOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TaskController;

class WorkTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }



    /**
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if ($request->begin) {
            $workTime = new PerformingTasks;
            $task = TaskOrder::find($request->task_id);

            $task->update(['in_work' => true]);

            $workTime->task_id = $request->task_id;
            $workTime->user_id = $request->user_id;
            $workTime->begin = Carbon::now();
            $workTime->save();
        }

        if ($request->finish) {

            $workTime = PerformingTasks::where('user_id', $request->user_id)
                ->where('count', null)
                ->where('finish', null);

            $task_id = DB::table('performing_tasks')
                ->select('task_id')
                ->where('user_id', $request->user_id)
                ->where('count', null)
                ->where('finish', null)->get();

            $status = $workTime->update([
                'count' => $request->count,
                'defects' => $request->defects,
                'finish' => Carbon::now()
            ]);

            $taskTime = new TaskController;

            $workTime = $taskTime->userWorkTime($task_id[0]->task_id);
            $workPaused = $taskTime->taskPaused($task_id[0]->task_id);
            $workWaiting = $taskTime->taskWaiting($task_id[0]->task_id);

            if ($status) {
                $task = TaskOrder::find($task_id[0]->task_id);
                $task->update([
                    'in_work' => false,
                    'user_count' => $task->user_count - $request->count
                ]);

                $cardId = $task->get(['card_id'])->pluck('card_id');

                $newCardTime = ($workTime - $workPaused - $workWaiting)/$request->count;
                $cardCurrentTime = TechCardTime::where('card_id', $cardId)->get('dynamic_time')[0]->dynamic_time;
                $updateCardTime = TechCardTime::where('card_id', $cardId);

                $updateCardTime->update([
                    "dynamic_time" => ($cardCurrentTime + $newCardTime)/2
                ]);
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
