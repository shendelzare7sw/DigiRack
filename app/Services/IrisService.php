<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;

class IrisService
{
    protected $creatorKey;
    protected $approverKey;
    protected $isProduction;
    protected $baseUrl;

    public function __construct()
    {
        // Midtrans IRIS uses Creator or Approver key. Usually for Payout API we use Approver key.
        $this->approverKey = SystemSetting::val('midtrans_iris_api_key', env('MIDTRANS_IRIS_API_KEY'));
        $this->isProduction = SystemSetting::val('midtrans_is_production', env('MIDTRANS_IS_PRODUCTION', false)) === 'true';

        $this->baseUrl = $this->isProduction 
            ? 'https://app.midtrans.com/iris/api/v1' 
            : 'https://app.sandbox.midtrans.com/iris/api/v1';
    }

    /**
     * Create Payout Request to Midtrans IRIS
     */
    public function createPayout($payoutData)
    {
        // Fallback for development if no API key is provided
        if (empty($this->approverKey)) {
            return [
                'success' => true,
                'data' => [
                    'reference_no' => 'DUMMY-IRIS-' . uniqid(),
                ],
                'message' => 'Dummy Iris Payout Created (No API Key)'
            ];
        }

        $response = Http::withBasicAuth($this->approverKey, '')
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/payouts", [
                'payouts' => [
                    [
                        'beneficiary_name' => $payoutData['beneficiary_name'],
                        'beneficiary_account' => $payoutData['beneficiary_account'],
                        'beneficiary_bank' => $payoutData['beneficiary_bank'],
                        'amount' => strval($payoutData['amount']), // iris expects string for amount
                        'notes' => $payoutData['notes'] ?? 'Digital Hook Payout',
                    ]
                ]
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'message' => $response->body(),
        ];
    }
}
