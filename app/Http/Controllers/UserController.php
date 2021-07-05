<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
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
            ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->join('roles', 'roles.id', '=', 'users_roles.role_id')
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'roles.slug')
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
        $department1 = Department::where('id', 1)->first();
        $department2 = Department::where('id', 2)->first();
        $department3 = Department::where('id', 3)->first();

        try {
            $user = new User();
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            $role = Role::where('slug', $request->slug)->first();
            $user->roles()->attach($role);

            if ($request->slug === 'collector') {
                $user->department()->attach($department3);
            } elseif ($request->slug === 'machine-operator') {
                $user->department()->attach($department1);
            } elseif ($request->slug === 'shareholder') {
                $user->department()->attach($department2);
            }

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
