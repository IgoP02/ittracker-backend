<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// use Spatie\Activitylog\Models\Activity;

class AuthController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {
        if (Auth::user()->username != "admin") {
            activity("auth_fail")->log("Intento de acceso no autorizado a cliente: ", $request->ip);
            abort(401);
        }
        $validatedData = $request->validate([
            "username" => "required|string|max:50|unique:users",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8",
            "name" => ["required", "string", "min:8", "max:20", "regex:/^[A-Z]\w+\b(\s[A-Z])?\s\b[A-Z]\w+/u"],
        ]);

        $user = User::create([
            "username" => $validatedData["username"],
            "email" => $validatedData["email"],
            "name" => $validatedData["name"],
            "password" => Hash::make($validatedData["password"]),
        ]);
        $token = $user->createToken("auth_token")->plainTextToken;

        activity("register")->log(
            "Analista " .
            $validatedData["name"] .
            " registrado como usuario " .
            $validatedData["username"]);

        return response()->json(
            [
                "access_token" => $token,
                "token_type" => "Bearer",
                "username" => $user->name,
            ]
        );
    }
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only(["username", "password"]))) {
            activity("auth_fail")->log("Intento de acceso no autorizado a cliente " . getHostByName(getHostName()));

            return response()->json(["message" => "invalid"], 401);
        }
        $user = User::where("username", $request["username"])->firstOrFail();
        $token = $user->createToken("auth_token")->plainTextToken;

        activity("login")->log($user->username);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            "username" => $user->username,
            "name" => $user->name,

        ]);
    }
    public function getUser(Request $request)
    {
        if (!$request->user()) {
            abort(401);
        }
        return $request->user();

    }
    public function logOut(Request $request)
    {
        activity("logout")->log($request->user()->username);

    }

    public function deleteUser(Request $request)
    {
        if (!$request->user()) {abort(401);}
        activity("user_deletion")->log("Usuario " . $request->username . " Eliminado");
        User::where("username", $request->query("username"))->delete();
    }

}
