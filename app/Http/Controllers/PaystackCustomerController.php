<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yabacon\Paystack;

class PaystackCustomerController extends Controller
{
    /**
     * Create a new customer on paystack
     */

    public function paystackCustomerCreate(Request $request)
    {
        $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));

        $data = Validator::make($request->all(), [
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($data->fails()) {
            return response()->json(['error' => $data->errors()], 400);
        }

        try {
            $customer = $paystack->customer->create([
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
            ]);

            return response()->json(['data' => $customer], 200);
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            return response()->json(['error' => [
                $e->getMessage(),
                $e->getRequestObject(),
            ]], 400);
        }
    }

    /**
     * List all customers on paystack
     */

    public function listCustomers()
    {
        $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));

        try {
            $customers = $paystack->customer->list();

            return response()->json(['data' => $customers], 200);
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            return response()->json(['error' => [
                $e->getMessage(),
                $e->getRequestObject(),
            ]], 400);
        }
    }

    /** 
     * Get a customer on paystack
     */

    public function getCustomer($code)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('PAYSTACK_PAYMENT_URL') . "/customer/$code",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer sk_test_1595d78a9189ea591a5e9f64542db9eac011245c",
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return response()->json(['data' => json_decode($response)], 200);
    }


    /**
     * Update a customer on paystack
     */
    
     public function updateCustomer(Request $request, $code)
     {
         $paystack = new \Yabacon\Paystack(env('PAYSTACK_SECRET_KEY'));
     
         // Validate incoming data
         $validatedData = $request->validate([
             'first_name' => 'nullable|string|max:255',
             'last_name' => 'nullable|string|max:255',
             'phone' => 'nullable|string|max:15',
         ]);
     
         try {
             // Pass the data as an array to the Paystack SDK
             $customer = $paystack->customer->update([
                 'code' => $code,
                 'first_name' => $validatedData['first_name'] ?? null,
                 'last_name' => $validatedData['last_name'] ?? null,
                 'phone' => $validatedData['phone'] ?? null,
             ]);
     
             return response()->json([
                 'message' => 'Customer updated successfully',
                 'data' => $customer->data,
             ], 200);
         } catch (\Yabacon\Paystack\Exception\ApiException $e) {
             return response()->json([
                 'error' => [
                     'message' => $e->getMessage(),
                     'details' => $e->getRequestObject(),
                 ],
             ], 400);
         }
     }
     
     
}
