<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends AbstractCrudController
{
    public string $table = "products";

    public array $validationRules = [
        "title" => "required|string|max:255",
        "description" => "required|string",
        "category_id" => "required|integer|exists:categories,id",
    ];

    public function edit(int $id): JsonResponse
    {
        return $this->_edit($id, [
            "select" => ["id", "title", "description", "category_id", "admin_id"]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->_store($request, $this->validationRules, [
            "admin_id" => Auth::guard("admin")->id(),
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
