<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DictionaryController extends Controller
{
    public array $permissions = [];

    public function __construct() {
        $this->permissions = Config::get('auth.permissions');
    }

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
