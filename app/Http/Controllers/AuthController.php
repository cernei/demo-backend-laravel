<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }

    public function getUser(): JsonResponse
    {
        $user = Auth::user();
        $role = DB::table('roles')
            ->select(['id', 'name', 'permissions'])
            ->find($user['role_id']);

        $user['permissions'] = array_merge(
            ['authorized'],
            json_decode($role->permissions)
        );

        return response()->json(['data' => $user]);
    }
}
