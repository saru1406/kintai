<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

final class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $user = Auth::user();

        // 既存のトークンを削除
        $user->tokens()->delete();

        // 新しいトークンを発行
        $token = $user->createToken('kintai')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
