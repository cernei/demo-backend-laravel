<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function run(Request $request): JsonResponse
    {
        $params = $request->all();

        $schemas = [
            'access_log' => [
                'paginate' => 10,
                'table' => 'access_log',
                'where' => ['id', 'ip'],
                'select' => ['id', 'url', 'ip', 'referer', 'user_agent', 'created_at'],
            ],
            'users' => [
                'paginate' => 10,
                'table' => 'users',
                'where' => ['id'],
                'select' => ['id', 'name', 'email', 'created_at'],
            ],
            'admins' => [
                'paginate' => 10,
                'table' => 'admins',
                'where' => ['id'],
                'select' => ['id', 'roles.name as role', 'name', 'email', 'created_at'],
                'join' => [
                    'roles' => ['admins.role_id', 'roles.id']
                ],
            ],
            'products' => [
                'paginate' => 10, // limit
                'join' => [
                    'categories' => ['products.category_id', 'categories.id'],
                ],
                'table' => 'products',
                'where' => [],
                'whereFullText' => ['description'],
                'select' => ['id', 'name', 'description', 'price', 'categories.name as category',  'created_at'],
            ],
            'stock_locations' => [
                'paginate' => 10, // limit
                'table' => 'stock_locations',
                'where' => [],
                'select' => ['id', 'code'],
            ],
            'stock_items' => [
                'paginate' => 10, // limit
                'table' => 'stock_items',
                'join' => [
                    'products' => ['stock_items.product_id', 'products.id'],
                    'stock_locations' => ['stock_items.location_id', 'stock_locations.id']
                ],
                'where' => ['products.name'],
                'select' => ['id', 'products.name as product', 'stock_locations.code as location', 'quantity'],
            ],
            'orders' => [
                'paginate' => 10, // limit
                'table' => 'orders',
                'where' => ['email'],
                'select' => ['id', 'email', 'total_amount', 'status '],
            ],
            'categories' => [
                'paginate' => 10, // limit
                'table' => 'categories',
                'where' => ['name'],
                'select' => ['id', 'name'],
            ],
            'attributes' => [
                'paginate' => 10, // limit
                'table' => 'attributes',
                'where' => ['name'],
                'select' => ['id', 'name', 'created_at'],
            ],
            'roles' => [
                'paginate' => 10, // limit
                'table' => 'roles',
                'where' => [],
                'join' => [
                    'admins' => ['roles.user_id', 'admins.id']
                ],
                'select' => ['id', 'name', 'permissions', 'admins.name as user', 'created_at'],
            ]
        ];
        $result = [];

        if (key_exists($params['entity'], $schemas)) {
            $schema = $schemas[$params['entity']];
            $joins = [];
            foreach($schema['select'] as $field) {
                if (str_contains($field, '.')) {
                    $joins[] = explode('.', $field)[0];
                }
            }
            if ($joins) {
                $newSelect = [];
                foreach($schema['select'] as $field) {
                    if (str_contains($field, '.') OR str_contains($field, ' ')) {
                        $newSelect[] = $field;
                    } else {
                        $newSelect[] = $schema['table'] . '.' . $field;
                    }
                }
                $schema['select'] = $newSelect;
            }
            $qb = DB::table($schema['table'])->selectRaw(implode(', ', $schema['select']));

            if (count($joins)) {
                foreach($joins as $joinTable) {
                    $qb = $qb->join($joinTable, $schema['join'][$joinTable][0], '=', $schema['join'][$joinTable][1]);
                }
            }

            if ($params['filters']) {
                foreach ($params['filters'] as $key => $filter) {
                    if ($filter['value']) {
                        if (in_array($key, $schema['where'])) {
                            $qb = $qb->where($key, 'like', $filter['value'] . '%');
                        }
                        if (isset($schema['whereFullText']) && in_array($key, $schema['whereFullText'])) {
                            $qb = $qb->whereRaw(
                                $key . "_search @@ plainto_tsquery('english', ?)",
                                [$filter['value']]
                            );
                        }
                    }
                }
            }
            $qb = $qb->orderBy('id', 'DESC');
            if ($params['sort']) {
                if (in_array($params['sort']['field'], $schema['select'])) {
                    $qb = $qb->orderBy($params['sort']['field'], $params['sort']['order'] === -1 ? 'DESC' : 'ASC');
                }
            }

            if ($schema['paginate']) {
                $result = $qb->paginate($schema['paginate'] ?? 10);
            } else {
                $result = $qb->get();
            }
        } else {
            return response()->json([
                'message' => 'No `' . $params['entity'] . '` schema found',
            ], 400);
        }

        return response()->json(['data' => $result]);
    }

}
