<?php

namespace App\Services;

interface BasePaymentService
{
    public function createPayment(array $bodyRequest): object;
}
