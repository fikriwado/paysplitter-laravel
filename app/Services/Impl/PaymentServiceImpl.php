<?php

namespace App\Services\Impl;

use App\Models\Application;
use App\Models\Transaction;
use App\Services\PaymentService;
use App\Services\PaymentXenditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentServiceImpl implements PaymentService
{
    private PaymentXenditService $xenditService;

    public function __construct(PaymentXenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    public function createTransaction(array $bodyRequest, string $appId, string $typeId): object
    {
        $response = $this->xenditService->createPayment($bodyRequest);
        $paysplitterPayload = $response->paysplitter_payload ?? null;
        $originalPayload = $response->original_payload ?? null;
        $isValidStatus = $paysplitterPayload->isValidStatus ?? null;
        $isValidResponse = $paysplitterPayload->isValidResponse ?? null;
        $application = Application::select('payment_gateway')->find($appId);

        if (!$isValidStatus || !$isValidResponse) {
            return response()->json([
                'success' => false,
                'message' => "Request is unprocessable"
            ], 422);
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'application_id' => $appId,
                'payment_type_id' => $typeId,
                'payment_url' => $paysplitterPayload->payment_url,
                'invoice_number' => $originalPayload->external_id,
                'amount' => $originalPayload->amount,
                'payment_gateway' => $application->payment_gateway,
                'status' => 'pending'
            ]);

            $transaction->transactionLogs()->create([
                'transaction_id' => $transaction->id,
                'status' => 'pending',
                'request_payload' => json_encode($bodyRequest),
                'response_payload' => json_encode($response)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'response' => $response
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create transaction failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Oops! An error occurred while adding the transaction.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
