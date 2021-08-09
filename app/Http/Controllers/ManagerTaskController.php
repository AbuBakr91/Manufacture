<?php

namespace App\Http\Controllers;

use App\Models\PerformingTasks;
use BeyondCode\LaravelWebSockets\Apps\App;
use Illuminate\Http\Request;
use App\Models\TaskOrder;
use Illuminate\Support\Facades\DB;
use App\Events\ShowTask;

class ManagerTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TaskOrder::all()->where('user_count', '!=', 0)
                                ->where('deleted', '=', '0');
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
        date_default_timezone_set('Europe/Moscow');

        $model = new TaskOrder;
        $request->dep_id ? $model->dep_id = $request->dep_id : null;
        $request->user_id ? $model->user_id = $request->user_id : null;
        $model->count = $request->counts;
        $model->user_count = $request->counts;
        $model->card_id = $request->card_id;
        $model->in_work = false;
        $model->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Support\Collection
     */
    public function show($id)
    {
        return DB::table('task_orders')
            ->join('technical_cards', 'task_orders.card_id', '=', 'technical_cards.id')
            ->select('task_orders.id', 'task_orders.dep_id', 'task_orders.count', 'task_orders.user_count', 'task_orders.card_id', 'technical_cards.name')
            ->where('task_orders.dep_id',  $id)
            ->where('task_orders.user_count', '>', 0)
            ->where('task_orders.deleted', '=', 0)
            ->get();
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = TaskOrder::where('id',$id);
        $perform_task = PerformingTasks::where('task_id', $id);
        $count = $perform_task->count();

        if ($count) {
            $task->update([ "deleted" => true]);
        } else {
            $task->delete();
        }

        return ["message" => true];
    }
}
