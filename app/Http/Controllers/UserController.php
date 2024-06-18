<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\UserDeportment;
use App\Models\UserRoles;
use App\Models\UserWorkRoom;
use App\Models\WorkRoom;
use App\Services\UserService;
use App\Services\UserServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{

    protected UserService $service;

    public function __construct(UserServiceInterface $service)
    {
        $this->service = $service;
    }
    /**
     * возвращает список пользователей
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return $this->service->getUser();
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
     * @throws \Exception
     */
    protected function roleForDeportment($deportment): string
    {
        switch ($deportment) {
            case 1:
                return 'machine-operator';
            case 2:
                return 'shareholder';
            case 3:
                return 'collector';
            default:
                return throw new \Exception();
        }
    }
    /**
     * создает пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {

            $this->service->addNewUser($request);

            $role = Role::where('slug', $this->roleForDeportment($request->slug))->first();

            $user->roles()->attach($role);

            $user->department()->attach(Department::where('id', $request->slug)->first());
            $user->workRoom()->attach(WorkRoom::where('id', $request->office)->first());

            return response()->json([
                "status" => true,
                "user" => $user
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                "status" => false,
                "message" => $exception->getMessage()
            ]);
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
        $this->service->showUser($params);
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
     * редактирует пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->service->editUser($request, $id);

        return response()->json([
            "status" => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->service->deleteUser($id);
    }

    protected function getUserName($user_id)
    {
        return User::find($user_id);
    }
}
