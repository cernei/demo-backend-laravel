<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view()->file(resource_path('views/dashboard.html'));
    }

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'orders' => [
                'unprocessed' => $this->unprocessedOrders(),
            ],
            'products' => [
                'bestSelling' => $this->bestSellingProduct(),
            ],
            'warehouse' => [
                'totalItems' => $this->stockTotalItems(),
            ],
        ]);
    }

    private function stockTotalItems(): int
    {
        return (int) DB::table('stock_items')->sum('quantity');
    }

    private function unprocessedOrders()
    {
        return DB::table('orders')
            ->select('id', 'email', 'status', 'total_amount', 'ordered_at', 'created_at')
            ->whereIn('status', ['pending', 'unprocessed'])
            ->orderByDesc('ordered_at')
            ->count();
    }

    private function bestSellingProduct()
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as units_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('units_sold')
            ->first();
    }

    public function data(): JsonResponse
    {
        $invoices = DB::table('invoices')
            ->select('id', 'invoice_id', 'customer_name', 'amount', 'currency', 'comment', 'shipping_status', 'shipping_info', 'created_at')
            ->orderByDesc('id')
            ->get();

        $jobs = DB::table('jobs')->orderByDesc('id')->get();
        $jobs->transform(static function ($job) {
            foreach (['created_at', 'available_at'] as $field) {
                if (!empty($job->{$field})) {
                    $job->{$field} = gmdate('Y-m-d H:i:s', (int) $job->{$field});
                }
            }

            return $job;
        });

        $failedJobs = DB::table('failed_jobs')->orderByDesc('id')->get();

        $eventLog = DB::table('event_log')->orderByDesc('id')->get();

        return response()->json([
            'invoices' => $invoices,
            'jobs' => $jobs,
            'failedJobs' => $failedJobs,
            'eventLog' => $eventLog,
        ]);
    }

    public function clearAll(): JsonResponse
    {
        DB::table('invoices')->truncate();
        DB::table('event_log')->truncate();
        DB::table('jobs')->truncate();
        DB::table('failed_jobs')->truncate();

        return response()->json([
            'message' => 'Tables truncated successfully.',
        ]);
    }
}
