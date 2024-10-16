<?php

namespace App\Services\API;

interface PaymentService
{
    public function createPayment(array $bodyRequest): object;
}
