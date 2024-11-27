<?php

namespace App\Http\Controllers\Webhooks;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\PaymentXenditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WebhookXenditController extends Controller
{
    private PaymentXenditService $xenditService;

    public function __construct(PaymentXenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    public function __invoke(Request $request)
    {
        // Set location id
        Carbon::setLocale('id');

        // Log request from xendit
        $requestBody = $request->getContent();
        Log::info('incoming-xendit', ['requestBody' => $requestBody]);

        // Validate response
        $response = $this->xenditService->getInvoices($request->external_id);
        if (!$response) {
            return response()->json(['message' => 'Invalid data transaction'], 401);
        }

        // Validate transaction
        $transaction = Transaction::where('invoice_number', $response->external_id)->first();
        if (!$transaction) {
            return response()->json(['message' => 'Invalid transaction'], 400);
        }

        // Validate is transaction has been processed
        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Your payment has been processed'], 400);
        }

        DB::beginTransaction();
        try {
            $webhookResponse = Http::post($transaction->subApplication->webhook_url, $response);
            $bodyResponse = $webhookResponse->body();
            $transactionLog = ['transaction_id' => $transaction->id];

            if ($webhookResponse->successful()) {
                $transaction->update(['status' => 'paid']);
                $transactionLog = array_merge($transactionLog, [
                    'status' => 'paid',
                    'request_payload' => json_encode($response),
                    'response_payload' => json_encode($webhookResponse->json())
                ]);
            } else {
                $errorStatus = $webhookResponse->status();
                $errorMessage = 'Sending notification failed: ';

                if ($errorStatus === 405) {
                    $errorMessage .= 'Method Not Allowed | The POST method is not supported for route.';
                }

                Log::info($errorMessage, ['response' => $bodyResponse]);
                $transactionLog = array_merge($transactionLog, [
                    'status' => 'failed',
                    'request_payload' => json_encode($response),
                    'response_payload' => json_encode(['message' => $errorMessage])
                ]);
            }

            $transaction->transactionLogs()->create($transactionLog);

            DB::commit();
            Log::info('Sending notification successfully.', ['response' => $bodyResponse]);
            return response()->json(['message' => 'Sending notification successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Sending notification failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => false, 'message' => 'Sending notification failed.'], 500);
        }
    }
}
