<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockItemsController extends AbstractCrudController
{
    public string $table = "stock_items";

    public array $validationRules = [
        "quantity" => "required|string|max:255",
    ];

    public function edit(int $id): JsonResponse
    {
        return $this->_edit($id, [
            "select" => ["id", "product_id", "location_id", "quantity"]
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
