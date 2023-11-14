<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryController extends Controller
{
    public function restoreDatabase(): JsonResponse
    {
        $filepath = base_path() . '/database/dumps/companies.sql';
        DB::unprepared(file_get_contents($filepath));

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function run(Request $request): JsonResponse
    {
        $params = $request->all();

        $schemas = [
            'users' => [
                'paginate' => 10,
                'table' => 'users',
                'where' => ['id'],
                'select' => ['id', 'name', 'email', 'created_at'],
            ],
            'posts' => [
                'paginate' => 10, // limit
                'join' => [
                    'categories' => ['posts.category_id', 'categories.id'],
                    'users' => ['posts.user_id', 'users.id']
                ],
                'table' => 'posts',
                'where' => ['categories.name'],
                'select' => ['id', 'content', 'categories.name as category', 'users.name as user', 'created_at'],
            ],
            'categories' => [
                'paginate' => 10, // limit
                'table' => 'categories',
                'where' => ['name'],
                'select' => ['id', 'name', 'created_at'],
            ],
            'roles' => [
                'paginate' => 10, // limit
                'table' => 'roles',
                'where' => [],
                'join' => [
                    'users' => ['roles.user_id', 'users.id']
                ],
                'select' => ['id', 'name', 'permissions', 'users.name as user', 'created_at'],
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
                foreach ($params['filters'] as $key => $filterValue) {
                    if (in_array($key, $schema['where'])) {
                        $qb = $qb->where($key, 'ilike', $filterValue['value'] . '%');
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
                $result = $qb->paginate($schema['paginate']);
            } else {
                $result = $qb->get();
            }
        }

        return response()->json(['data' => $result]);
    }

}
