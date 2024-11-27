<?php

namespace App\Services\Impl;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use App\Services\PaymentXenditService;

class PaymentXenditServiceImpl implements PaymentXenditService
{
    private $apiInstance;

    public function __construct()
    {
        $this->apiInstance = new InvoiceApi();
        Configuration::setXenditKey(config('payments.xendit.secret_key'));
    }

    public function createPayment(array $bodyRequest): object
    {
        $create_invoice_request = new CreateInvoiceRequest($bodyRequest);
        try {
            $response = $this->apiInstance->createInvoice($create_invoice_request);
            return json_decode(json_encode([
                'paysplitter_payload' => [
                    'isValidResponse' => isset($response['id']),
                    'isValidStatus' => $response['status'] === 'PENDING',
                    'payment_url' => $response['invoice_url'],
                ],
                'original_payload' => $response
            ]));
        } catch (\Xendit\XenditSdkException $e) {
            echo 'Exception when calling InvoiceApi->createInvoice: ', $e->getMessage(), PHP_EOL;
            echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
        }
    }

    public function getInvoices($external_id, $for_user_id = null): object
    {
        try {
            $response = $this->apiInstance->getInvoices($for_user_id, $external_id);
            return (object) json_decode(json_encode($response[0]));
        } catch (\Xendit\XenditSdkException $e) {
            return response()->json([
                'message' => 'Invalid data transaction',
                'error' => $e->getFullError()->errorCode
            ], 400);
        }
    }
}
