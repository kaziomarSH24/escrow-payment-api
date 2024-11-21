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


    public function createCustomer($data)
    {
        $curl = curl_init();
        // dd(json_decode($data)->email);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "{$this->apiUrl}/customer",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->email . ":" . $this->apiKey,
            // CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);

        // $response = json_decode($response);
        // dd($response->id);
       
        return json_decode($response);
        curl_close($curl);

    }

    public function createTransaction($data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "{$this->apiUrl}/transaction",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->email . ":" . $this->apiKey,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        // dd($response);

        return json_decode($response);
        curl_close($curl);
        
    }

    public function getTransaction($transactionId)
    {
        $url = $transactionId != null ? "{$this->apiUrl}/transaction/{$transactionId}" : "{$this->apiUrl}/transaction";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->email . ":" . $this->apiKey,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        // dd($response);

        return json_decode($response);
        curl_close($curl);
    }
}
