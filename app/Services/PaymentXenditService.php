<?php

namespace App\Services;

interface PaymentXenditService extends BasePaymentService
{
    public function createPayment(array $bodyRequest): object;
    public function getInvoices($external_id, $for_user_id = null): object;
}
