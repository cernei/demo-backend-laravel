<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DictionaryController extends Controller
{
    public array $permissions = [
        'categories.view' => 'View categories',
        'categories.create' => 'Create categories',
        'categories.edit' => 'Edit categories',
        'categories.delete' => 'Delete categories',

        'posts.view' => 'View posts',
        'posts.create' => 'Create posts',
        'posts.edit' => 'Edit posts',
        'posts.delete' => 'Delete posts',

        'users.view' => 'View users',
        'users.create' => 'Create users',
        'users.edit' => 'Edit users',
        'users.delete' => 'Delete users',

        'roles.view' => 'View roles',
        'roles.create' => 'Create roles',
        'roles.edit' => 'Edit roles',
        'roles.delete' => 'Delete roles',

    ];
    public function getDictionary(string $dictionaryName): JsonResponse
    {

        $schemas = [
            'categories' => ['table' => 'categories', 'keys' => ['id', 'name']],
            'roles' => ['table' => 'roles', 'keys' => ['id', 'name']],
            'permissions' => ['static' => 'permissions'],
        ];
        $filteredData = [];

        if (key_exists($dictionaryName, $schemas)) {
            $schema = $schemas[$dictionaryName];
            if (isset($schema['table'])) {
                $result = DB::table($schema['table'])->get();
                foreach ($result as $item) {
                    $filteredData[] = [
                        'id' => $item->{$schema['keys'][0]},
                        'name' => $item->{$schema['keys'][1]},
                    ];
                }
            } else {
                $result = $this->{$schema['static']};
                foreach ($result as $key => $value) {
                    $filteredData[] = [
                        'id' => $key,
                        'name' => $value,
                    ];
                }
            }
        }

        return response()->json(['data' => $filteredData]);
    }

}
