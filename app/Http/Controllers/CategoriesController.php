<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriesController extends AbstractCrudController
{
    public string $table = 'categories';
    public array $validationRules = [
        'name' => 'string|unique:categories',
    ];

    public function edit(int $id): JsonResponse
    {
        return $this->_edit($id, [
            'select' => ['id', 'name']
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->_store($request, $this->validationRules);
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
