<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\UserDeportment;
use App\Models\UserRoles;
use App\Models\UserWorkRoom;
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

    protected function roleForDeportment($deportment): string
    {
        switch ($deportment) {
            case 1:
                return 'machine-operator';
                break;
            case 2:
                return 'shareholder';
                break;
            case 3:
                return 'collector';
                break;
        }
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
        return DB::table('users')
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->leftJoin('user_work_rooms', 'user_work_rooms.user_id', '=', 'users.id')
            ->leftJoin('users_departments', 'users_departments.user_id', '=', 'users.id')
            ->leftJoin('work_rooms', 'user_work_rooms.work_room_id', '=', 'work_rooms.id')
            ->select('users_departments.department_id as deportment', 'users.id', 'users.firstname', 'users.lastname', 'users.email', 'work_rooms.id as office')
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
            $user->update(["firstname" => $request->firstname]);
        }

        if ($request->lastname){
            $user->update(["lastname" => $request->lastname]);
        }

        if ($request->email){
            $user->update(["email" => $request->email]);
        }

        if ($request->password){
            $user->update(["password" => bcrypt($request->password)]);
        }

        if ($request->deportment){
            $role = Role::where('slug', $this->roleForDeportment($request->deportment))->get()[0]->id;
            $userRole = UserRoles::where('user_id', $id);
            $userRole->update([
                "role_id" => $role
            ]);

            $userDepot = UserDeportment::where('user_id', $id);
            $userDepot->update([
                'department_id' => $request->deportment
            ]);
        }

        if($request->office) {
            $userOffice = UserWorkRoom::where('user_id', $id);
            $userOffice->update([
                'user_id' => $id,
                'work_room_id' => $request->office
            ]);
        }

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
