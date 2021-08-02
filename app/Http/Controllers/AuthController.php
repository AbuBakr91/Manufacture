<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public $loginAfterSignUp = true;

    public function login(Request $request)
    {
        $credentials = $request->only("email", "password");
        $token = null;

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                "status" => false,
                "message" => "Неверный логин или пароль!"
            ]);
        }

        return response()->json([
            "status" => true,
            "token" => $token,
            "user" => $this->getUserAndRole($request->email)[0]
        ]);
    }

    protected function getUserAndRole($email)
    {
        return DB::table('users')
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->leftJoin('user_work_rooms', 'user_work_rooms.user_id', '=', 'users.id')
            ->leftJoin('work_rooms', 'user_work_rooms.work_room_id', '=', 'work_rooms.id')
            ->leftJoin('users_departments', 'users_departments.user_id', '=', 'users.id')
            ->select('users_departments.department_id', 'users.id', 'users.firstname', 'users.lastname', 'roles.slug', 'work_rooms.name as office')
            ->where('users.email', $email)
            ->get();
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|string|min:6|max:10"
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            "status" => true,
            "user" => $user
        ]);
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            "token" => "required"
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                "status" => true,
                "message" => "User logged out successfully"
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                "status" => false,
                "message" => "Ops, the user can not be logged out"
            ]);
        }
    }
}
