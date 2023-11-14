<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsersController extends AbstractCrudController
{
    public string $table = 'users';

    public function edit(int $id): JsonResponse
    {
        return $this->_edit($id, [
            'select' => ['id', 'name', 'role_id', 'email', 'created_at']
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->_store($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role_id' => ['required', 'integer'],
            'password' => ['required', Rules\Password::defaults()],
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($request, $id, [
            'name' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'integer'],
            'password' => [],
        ], function ($item) {
            if ($item['password']) {
                $item['password'] = Hash::make($item['password']);
            }
            return $item;
        });
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->_destroy($id);
    }
}
