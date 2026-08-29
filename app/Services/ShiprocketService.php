<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShiprocketService
{
    protected $baseUrl = "https://apiv2.shiprocket.in/v1/external";
    protected $token;

    public function __construct()
    {
        $this->authenticate();
    }

    private function authenticate()
    {
        try {
            $response = Http::post($this->baseUrl.'/auth/login', [
                'email' => config('services.shiprocket.email'),
                'password' => config('services.shiprocket.password'),
            ]);

            $this->token = $response->json()['token'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Authentication Error: ' . $e->getMessage());
            $this->token = null;
        }
    }

    public function createShipment($orderData)
    {
        try {
            if (!$this->token) {
                return ['status'=>'error', 'message' => 'Shiprocket authentication failed'];
            }
            $return = Http::withToken($this->token)
                ->post($this->baseUrl.'/orders/create/adhoc', $orderData)
                ->json();
            return ['status'=>'success', 'data'=>$return];
        } catch (\Exception $e) {
            Log::error('Shiprocket API Error: ' . $e->getMessage());
            return ['status'=>'error', 'message' => $e->getMessage()];
        }
    }


    public function checkServiceability($pickupPincode, $deliveryPincode, $weight = 1, $cod = 0)
    {
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . '/courier/serviceability', [
                'pickup_postcode' => $pickupPincode,
                'delivery_postcode' => $deliveryPincode,
                'cod' => $cod,
                'weight' => $weight,
            ]);

        return $response->json();
    }
}
