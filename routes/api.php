<?php

use App\Http\Controllers\OperationTaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TechnicalCardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ManagerTaskController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\DepartmentTaskController;
use App\Http\Controllers\WorkTimeController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource("tech_card", TechnicalCardController::class);
Route::resource("user_task", DepartmentTaskController::class);
Route::post("login", [AuthController::class, 'login']);
Route::post("add-paused", [TaskController::class, 'addPaused']);
Route::post("add-waiting", [TaskController::class, 'addWaiting']);
Route::get("cards", [CardController::class, 'index']);

Route::get("paused", [TaskController::class, 'userTaskPaused']);
Route::get("journal", [TaskController::class, 'adminJournal']);
Route::get("operation", [TaskController::class, 'technicalOperation']);
Route::post("operation-status", [TaskController::class, 'taskOperationStatus']);

Route::get("cards/{id}", [CardController::class, 'getCardByCategory']);



Route::post("material", [OperationTaskController::class, 'operationMoySklad']);


Route::get("task-status/{id}", [TaskController::class, 'taskStatusUser']);
Route::get("current-task/{id}", [TaskController::class, 'currentTask']);
Route::get("categories", [CategoryController::class, 'index']);
Route::get("departments", [DepartmentsController::class, 'index']);
Route::resource('users', UserController::class);
Route::resource('manager-task', ManagerTaskController::class);
Route::resource('work-time', WorkTimeController::class);

