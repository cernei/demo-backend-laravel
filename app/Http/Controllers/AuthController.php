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
    public function getUser(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $role = DB::table('roles')
                ->select(['id', 'name', 'permissions'])
                ->find($user['role_id']);

            $user['permissions'] = array_merge(
                ['authorized'],
                json_decode($role->permissions)
            );
            return response()->json(['data' => $user]);

        } catch(\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
