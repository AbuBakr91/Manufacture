<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PerformingTasks;
use App\Models\TaskOrder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Cloner\Data;
use function GuzzleHttp\Promise\task;

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
     * Store a newly created resource in storage.
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
                'finish' => Carbon::now()
            ]);


            if ($status) {
                $task = TaskOrder::find($task_id[0]->task_id);
                $task->update([
                    'in_work' => false,
                    'user_count' => $task->user_count - $request->count
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
