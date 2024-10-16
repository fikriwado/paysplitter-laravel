<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\API\PaymentXenditService;

class PaymentController extends Controller
{
    private PaymentXenditService $xenditService;

    public function __construct(PaymentXenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    public function payment(Request $request, $app, $sub = null)
    {
        $response = $this->xenditService->createPayment($request->all());
        return $response;
    }
}
