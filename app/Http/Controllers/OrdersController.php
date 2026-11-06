<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrdersController extends AbstractCrudController
{
    public string $table = "orders";

    public array $validationRules = [
    ];

    public function edit(int $id): JsonResponse
    {
        $result = Order::query()
            ->select('id', 'email', 'total_amount')
            ->with([
                'items',
                'items.product:id,name',
            ])
            ->find($id);

        $result->setRelation(
            'items',
            $result->items->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => number_format($item->price * $item->quantity, 2, '.', ''),
                ];
            })
        );

        return response()->json(['data' => $result]);
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
