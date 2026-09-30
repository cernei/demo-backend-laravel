<?php

namespace App\Http\Controllers;

use App\Jobs\ShipmentJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InvoicesController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event' => ['required', 'in:invoice.created'],
            'invoice_id' => ['required', 'string'],
            'customer' => ['required', 'array'],
            'amount' => ['required', 'numeric'],
            'currency' => ['required', 'in:USD,EUR,CHF'],
            'status' => ['required'],
            'due_date' => ['required', 'date_format:Y-m-d'],
            'created_at' => ['required'],
            'customer.id' => ['required'],
            'customer.name' => ['required', 'string'],
            'customer.email' => ['required', 'email'],
            'comment' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $validator->validated();

        if (DB::table('invoices')->where('invoice_id', $payload['invoice_id'])->exists()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Invoice event already processed (duplicate webhook).',
            ]);
        }

        try {
            $invoiceId = DB::transaction(function () use ($payload) {
                return DB::table('invoices')->insertGetId([
                    'invoice_id' => $payload['invoice_id'],
                    'customer_id' => $payload['customer']['id'],
                    'customer_name' => $payload['customer']['name'],
                    'customer_email' => $payload['customer']['email'],
                    'amount' => $payload['amount'],
                    'currency' => $payload['currency'],
                    'comment' => $payload['comment'],
                    'status' => $payload['status'],
                    'due_date' => $payload['due_date'],
                    'shipping_status' => ShipmentJob::PENDING,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        } catch (Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }

        ShipmentJob::dispatch((string) $payload['invoice_id'])->onQueue('shipments');

        return response()->json([
            'status' => 'success',
            'invoice_id' => $invoiceId,
        ]);
    }
}
