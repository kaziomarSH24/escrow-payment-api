<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EscrowService
{
    protected $apiUrl;
    protected $apiKey;
    protected $email;

    public function __construct()
    {
        $this->apiUrl = env('ESCROW_API_URL');
        $this->apiKey = env('ESCROW_API_KEY');
        $this->email = env('ESCROW_API_EMAIL');
    }

    //generic method to send request to escrow api for all endpoints
    protected function sendRequest($method, $endpoint, $data = null)
    {
        $curl = curl_init();
        $options = [
            CURLOPT_URL => "{$this->apiUrl}{$endpoint}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->email . ":" . $this->apiKey,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;
        } elseif ($method === 'GET') {
            $options[CURLOPT_HTTPGET] = true;
        } elseif ($method === 'PATCH') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PATCH';
            $options[CURLOPT_POSTFIELDS] = $data;
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }

    //create customer
    public function createCustomer($data)
    {
        return $this->sendRequest('POST', '/customer', $data);
    }

    //update customer
    public function updateCustomer($customerId, $data)
    {
        return $this->sendRequest('PATCH', "/customer/{$customerId}", $data);
    }

    //create transaction
    public function createTransaction($data)
    {
        return $this->sendRequest('POST', '/transaction', $data);
    }

    //get all transaction or get a single transaction
    public function getTransaction($transactionId)
    {
        if ($transactionId) {
            return $this->sendRequest('GET', "/transaction/{$transactionId}");
        } else {
            return $this->sendRequest('GET', "/transaction");
        }
    }

    //Listing partner transactions
    public function getPartnerTransactions()
    {
        return $this->sendRequest('GET', "/partner/transactions");
    }

    //Agree transaction
    public function aggreTransaction($transactionId, $data)
    {
        return $this->sendRequest('PATCH', "/transaction/{$transactionId}", $data);
    }
}
