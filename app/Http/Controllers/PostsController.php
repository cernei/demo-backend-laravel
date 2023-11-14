<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostsController extends AbstractCrudController
{
    public string $table = 'posts';
    public array $validationRules = [
        'content' => 'string',
        'category_id' => 'integer',
    ];

    public function edit(int $id): JsonResponse
    {
        return $this->_edit($id, [
            'select' => ['id', 'content', 'category_id']
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        return $this->_store($request, $this->validationRules,[
            'user_id' => $user['id']
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($request, $id, $this->validationRules);
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->_destroy($id);
    }
}
