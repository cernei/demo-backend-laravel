<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class JobTestController extends Controller
{
    public function shipment(): JsonResponse
    {
        Artisan::call('queue:work', [
            '--queue' => 'shipments',
            '--max-jobs' => 1,
            '--stop-when-empty' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Processed pending jobs in the shipments queue.',
        ]);
    }
}
