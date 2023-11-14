<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbstractCrudController extends Controller
{
    protected function _edit(int $id, $query): JsonResponse
    {
        try {
            if (isset($query['select'])) {
                $result = DB::table($this->table)
                    ->select($query['select'])
                    ->find($id);
            } else {
                $result = $query['result'];
            }

            return response()->json(['data' => $result]);
        } catch (\Throwable $error) {
            return response()->json(['message' => $error->getMessage()], 400);
        }
    }

    public function _store(Request $request, array $rules, array $additions = []): JsonResponse
    {
        try {
            $input = $request->validate($rules);
            $input = array_merge($input, $additions);
            DB::table($this->table)->insert($input);

            return response()->json(['message' => 'ok']);
        } catch (\Throwable $error) {
            return response()->json(['message' => $error->getMessage()], 400);
        }
    }

    public function _update(Request $request, int $id, array $rules, Closure $serializer = null): JsonResponse
    {
        try {
            $input = $request->validate($rules);
            if ($serializer) {
                $input = $serializer($input);
            }
            DB::table($this->table)->where('id', $id)->update($input);

            return response()->json(['message' => 'ok']);
        } catch (\Throwable $error) {
            return response()->json(['message' => $error->getMessage()], 400);
        }
    }

    public function _destroy(int $id): JsonResponse
    {
        try {
            DB::table($this->table)->delete($id);

            return response()->json(['message' => 'success']);
        } catch (\Throwable $error) {
            return response()->json(['message' => $error->getMessage()], 400);
        }
    }
}
