<?php

namespace App\Services\API\Impl;

use App\Services\API\PaymentXenditService;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

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
            return (object) [
                'paysplitter_payload' => [
                    'isValidResponse' => isset($response['id']),
                    'isValidStatus' => $response['status'] === 'PENDING',
                    'payment_url' => $response['invoice_url'],
                ],
                'original_payload' => $response
            ];
        } catch (\Xendit\XenditSdkException $e) {
            echo 'Exception when calling InvoiceApi->createInvoice: ', $e->getMessage(), PHP_EOL;
            echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
        }
    }
}
