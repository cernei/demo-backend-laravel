<?php

namespace App\Jobs;

use App\Exceptions\ShippingValidationException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ShipmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public const PENDING = 'PENDING';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const TECHNICAL_ERROR = 'TECHNICAL_ERROR';
    public const SUCCESS = 'SUCCESS';

    public function __construct(public readonly string $invoiceId)
    {
    }

    public function handle(): void
    {
        $invoice = DB::table('invoices')
            ->where('invoice_id', $this->invoiceId)
            ->first();

        if ($invoice === null) {
            return;
        }

        try {
            $url = (string) env('SHIPMENT_ENDPOINT') . '?case=' . urlencode((string) $invoice->comment);
            $response = Http::timeout(4)
                ->withHeaders(['Idempotency-Key' => 'shipment:' . $invoice->invoice_id])
                ->post($url, [
                    'invoice_id' => $invoice->invoice_id,
                    'amount' => $invoice->amount,
                    'name' => $invoice->customer_name,
                    'email' => $invoice->customer_email,
                ]);

            $payload = $this->decodePayload($response);
            $code = $response->status();

            if ($code >= 400 && $code < 500) {
                throw new ShippingValidationException(strtolower((string) ($payload['message'] ?? '')));
            }
            if ($code >= 500) {
                throw new RuntimeException('Server error');
            }
            if (! isset($payload['reference'])) {
                throw new ShippingValidationException('No reference number');
            }

            $this->recordAttempt($invoice, self::SUCCESS, 'Shipping info: ' . $payload['reference'], [
                'shipping_status' => self::SUCCESS,
                'shipping_info' => $payload['reference'],
            ]);
        } catch (ShippingValidationException $exception) {
            $this->recordAttempt($invoice, self::VALIDATION_ERROR, $exception->getMessage(), [
                'shipping_status' => self::VALIDATION_ERROR,
                'shipping_info' => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            $this->recordAttempt($invoice, self::TECHNICAL_ERROR, $exception->getMessage(), [
                'shipping_status' => self::TECHNICAL_ERROR,
            ]);
            $error = sprintf(
                '%s in %s:%d',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
            throw new RuntimeException($error, (int) $exception->getCode());
        }
    }

    private function decodePayload(Response $response): array
    {
        $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($payload)) {
            throw new ShippingValidationException('Invalid response payload');
        }

        return $payload;
    }

    private function recordAttempt(object $invoice, string $status, string $message, array $invoiceUpdates): void
    {
        DB::transaction(function () use ($invoice, $status, $message, $invoiceUpdates): void {
            DB::table('invoices')
                ->where('id', $invoice->id)
                ->update($invoiceUpdates + ['updated_at' => now()]);

            DB::table('event_log')->insert([
                'name' => 'shipping_attempt',
                'entity_id' => $invoice->id,
                'payload' => json_encode([
                    'invoice_id' => $invoice->invoice_id,
                    'status' => $status,
                    'message' => $message,
                ], JSON_THROW_ON_ERROR),
                'created_at' => now(),
            ]);
        });
    }
}
