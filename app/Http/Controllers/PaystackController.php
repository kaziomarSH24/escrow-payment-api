<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yabacon\Paystack;

class PaystackController extends Controller
{

    public function initialize(Request $request)
    {

        $paystack = new \Yabacon\Paystack(env('PAYSTACK_SECRET_KEY'));

        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric',
        ]);

        try {
            $tranx = $paystack->transaction->initialize([
                'amount' => $request->amount * 100, // Amount in Kobo
                'email' => $request->email,
                'callback_url' => route('paystack.callback'),
            ]);

            return response()->json(['data' => $tranx], 200);
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            return response()->json(['error' => [
                $e->getMessage(),
                $e->getRequestObject(),
            ]], 400);
        }
    }

    public function verify(Request $request)
    {
        $paystack = new \Yabacon\Paystack(env('PAYSTACK_SECRET_KEY'));
        // dd($request->all());
        $request->validate([
            'reference' => 'required',
        ]);

        try {
            $tranx = $paystack->transaction->verify([
                'reference' => $request->reference,
                'callback_url' => route('paystack.callback'),
            ]);

            return response()->json(['data' => $tranx], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * list all transactions
     */
    public function listTransactions()
    {
        $paystack = new \Yabacon\Paystack(env('PAYSTACK_SECRET_KEY'));

        try {
            $transactions = $paystack->transaction->getList();
            return response()->json(['data' => $transactions], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * fetch transaction by id
     */
    public function  fetchTranscation($id)
    {
        $paystack = new \Yabacon\Paystack(env('PAYSTACK_SECRET_KEY'));

        try {
            $tranx = $paystack->transaction->fetch(['id' => $id]);

            return response()->json(['data' => $tranx], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * export transactions
     */

    public function exportTransactions()
    {
        $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));
        try {
            $transactions = $paystack->transaction->export();
            return response()->json(['data' => $transactions], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    //callback function
    public function callback(Request $request)
    {
        $paystack = new \Yabacon\Paystack(env('PAYSTACK_SECRET_KEY'));

        $tranx = $paystack->transaction->verify([
            'reference' => $request->query('reference'),
        ]);

        dd($tranx);

        if ('success' === $tranx->data->status) {
            return response()->json(['message' => 'Payment successful', 'data' => $tranx], 200);
        }

        return response()->json(['message' => 'Payment failed', 'data' => $tranx->data], 400);
    }
}
