<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\UserDeportment;
use App\Models\UserRoles;
use App\Models\UserWorkRoom;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{

    public function getUser(): \Illuminate\Support\Collection
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

    public function addNewUser(Request $request): void
    {
        $user = new User();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
    }

    public function showUser(int $id): \Illuminate\Support\Collection
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

    public function editUser(Request $request, int $id): void
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
    }

    public function deleteUser(int $id): void
    {
        $user = User::find($id);
        $user->delete();
    }
}
