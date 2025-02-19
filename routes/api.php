<?php
declare(strict_types=1);

use App\Http\Controllers\LoggerController;
use App\Http\Controllers\OperationTaskController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\WebHooksController;
use App\Http\Controllers\WorkRoomController;
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

Route::resource('users', UserController::class);
Route::resource('logging', LoggerController::class);
Route::resource('work-time', WorkTimeController::class);
Route::resource("tech_card", TechnicalCardController::class);
Route::resource("user_task", DepartmentTaskController::class);
Route::resource('manager-task', ManagerTaskController::class);

Route::post("login", [AuthController::class, 'login']);
Route::post("add-paused", [TaskController::class, 'addPaused']);
Route::post("add-waiting", [TaskController::class, 'addWaiting']);
Route::post("date-range", [TaskController::class, 'getDataForDate']);
Route::post("webhook-create", [WebHooksController::class, 'webHook']);
Route::post("defects", [OperationTaskController::class, 'operationDefects']);
Route::post("sales-ratio/{id}", [StocksController::class, 'editSalesRatio']);
Route::post("material", [OperationTaskController::class, 'operationMoySklad']);
Route::post("operation-status", [TaskController::class, 'taskOperationStatus']);
Route::post("record-time", [TechnicalCardController::class, 'recordStaticTime']);
Route::post("update-card/{id}", [TechnicalCardController::class, 'updateTechCard']);
Route::post("retail-shift", [OperationTaskController::class, 'operationRetailShift']);

Route::get("cards", [CardController::class, 'index']);
Route::get("sales-ratio", [StocksController::class, 'index']);
Route::get("categories", [CategoryController::class, 'index']);
Route::get("journal", [TaskController::class, 'adminJournal']);
Route::get("work-rooms", [WorkRoomController::class, 'index']);
Route::get("departments", [DepartmentsController::class, 'index']);
Route::get("operation", [TaskController::class, 'technicalOperation']);
Route::get("cards/{id}", [CardController::class, 'getCardByCategory']);
Route::get("current-task/{id}", [TaskController::class, 'currentTask']);
Route::get("task-status/{id}", [TaskController::class, 'taskStatusUser']);
Route::get("record-card", [TechnicalCardController::class, 'recordTimeTechCard']);
Route::get("update-all-cards", [TechnicalCardController::class, 'updateAllTechCards']);


