<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function payment(Request $request, $app, $type = null)
    {
        $application = Application::select('id')->where('app_key', $app)->first();
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $type = $type ?? 'default';
        $paymentType = $application->paymentTypes()->select('id')->where('type', $type)->first();
        if (!$paymentType) {
            return response()->json([
                'success' => false,
                'message' => "Payment type $type not found"
            ], 404);
        }

        return $this->paymentService->createTransaction(
            $request->all(),
            $application->id,
            $paymentType->id
        );
    }
}
