<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\UserDeportment;
use App\Models\WorkRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * возвращает список пользователей
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DB::table('users')
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->leftJoin('user_work_rooms', 'user_work_rooms.user_id', '=', 'users.id')
            ->leftJoin('work_rooms', 'user_work_rooms.work_room_id', '=', 'work_rooms.id')
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'roles.slug', 'work_rooms.name')
            ->where('roles.slug', '!=', 'manager')
            ->get();
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
     * создает пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = new User();
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            switch ($request->slug) {
                case 1:
                    $roleSlug = 'machine-operator';
                    break;
                case 2:
                    $roleSlug = 'shareholder';
                    break;
                case 3:
                    $roleSlug = 'collector';
                    break;
            }

            $role = Role::where('slug', $roleSlug)->first();

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
        return DB::table('users')
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->leftJoin('user_work_rooms', 'user_work_rooms.user_id', '=', 'users.id')
            ->leftJoin('users_departments', 'users_departments.user_id', '=', 'users.id')
            ->leftJoin('work_rooms', 'user_work_rooms.work_room_id', '=', 'work_rooms.id')
            ->select('users_departments.id as deportment', 'users.id', 'users.firstname', 'users.lastname', 'users.email', 'roles.slug', 'work_rooms.name')
            ->where('users.id', $id)
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
     * редактирует пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($request->firstname){
            $user->firstname = $request->firstname;
        }

        if ($request->lastname){
            $user->firstname = $request->lastname;
        }

        if ($request->email){
            $user->email = $request->email;
        }

        if ($request->password){
            $user->password = bcrypt($request->password);
        }

        if ($request->slug){
            $role = Role::where('slug', $request->slug)->first();
            $user->roles()->attach($role);
        }

        $user->save();

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
        $user = User::find($id);
        $user->delete();
    }

    protected function getUserName($user_id)
    {
        return User::find($user_id);
    }
}
