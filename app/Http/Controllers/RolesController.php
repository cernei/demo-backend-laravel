<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RolesController extends AbstractCrudController
{
    public string $table = 'roles';
    public array $validationRules = [
        'name' => 'required|min:3',
        'permissions' => 'required|array|min:1',
    ];

    public function edit(int $id): JsonResponse
    {
        try {
            $result = DB::table($this->table)
                ->select(['id', 'name', 'permissions'])
                ->find($id);

            $result->permissions = json_decode($result->permissions);

            return response()->json(['data' => $result]);
        } catch (\Throwable $error) {
            return response()->json(['message' => $error->getMessage()], 400);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        return $this->_store($request, $this->validationRules, [
           'user_id' => $user['id'],
           'permissions' => json_encode($request->permissions),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($request, $id, $this->validationRules, function($item) {
            $item['permissions'] = json_encode($item['permissions']);
            return $item;
        });
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->_destroy($id);
    }
}
