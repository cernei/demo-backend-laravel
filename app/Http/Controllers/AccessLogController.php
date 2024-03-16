<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessLogController extends Controller
{
    public function trackView(Request $request): JsonResponse
    {
        if ($request->get('url')) {
            $arr = [
                'url' => $request->get('url'),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'referer' => $_SERVER['HTTP_REFERER'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT']
            ];

            DB::table('access_log')->insert($arr);
        }

        return response()->json(['message' => 'OK']);
    }
}
