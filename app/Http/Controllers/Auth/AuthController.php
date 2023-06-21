<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {
        if (Auth::user()->username != "admin") {
            return response(null, 401);
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
            return response()->json(["message" => "invalid"], 401);
        }
        $user = User::where("username", $request["username"])->firstOrFail();
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            "username" => $user->username,
        ]);
    }
    public function getUser(Request $request)
    {
        return response()->json($request->user()->username);

    }
    public function logOut(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();

    }

    public function deleteUser(Request $request)
    {
        User::where("username", $request->query("username"))->delete();
    }

}
