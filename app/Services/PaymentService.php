<?php

namespace App\Services;

interface PaymentService
{
    public function createTransaction(array $bodyRequest, string $appId, string $typeId): object;
}
