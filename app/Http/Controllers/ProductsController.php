<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends AbstractCrudController
{
    public string $table = "products";

    public array $validationRules = [
        "name" => "required|string|max:255",
        "description" => "required|string",
        "price" => "required|numeric",
        "category_id" => "required|integer|exists:categories,id",
    ];

    public function edit(int $id): JsonResponse
    {
        return $this->_edit($id, [
            "select" => ["id", "name", "description", "price", "category_id"]
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
