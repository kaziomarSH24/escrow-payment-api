<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    protected $escrowService;

    public function __construct(EscrowService $escrowService)
    {
        $this->escrowService = $escrowService;
    }


    public function createCustomer(Request $request){
        $data = Validator::make($request->all(), [
            'phone' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'string',
            'last_name' => 'required|string',
            'address' => 'required|array',
            'address.city' => 'required|string',
            'address.post_code' => 'required|string',
            'address.country' => 'required|string',
            'address.line1' => 'required|string',
            'address.line2' => 'string',
            'address.state' => 'required|string',
            'email' => 'required|email|unique:customers,email',
        ]);

        if ($data->fails()) {
            return response()->json(['error' => $data->errors()], 400);
        }

        $data = json_encode($data->validated());
        // dd($data);

        $response = $this->escrowService->createCustomer($data);
        if (isset($response->error)) {
            return response()->json(['error' => $response->error], 400);
        }

        if (isset($response->id)) {
            $data = json_decode($data, true);
            $name = $data['first_name'] . ' ' . ($data['middle_name'] ?? '') . ' ' . $data['last_name'];
            $name = trim(preg_replace('/\s+/', ' ', $name)); // Remove extra spaces if middle name is not present
           $customer =  Customer::create([
                'customer_id' => $response->id,
                'email' => $data['email'],
                'name' => $name,
                'phone' => $data['phone'],
                'address' => json_encode($data['address']),
            ]);
            return response()->json(['message' => 'Customer created successfully!', 'customer' => $customer], 201);
        }

        return response()->json(['error' => 'Failed to create customer'], 400);

    }




    public function createTransaction(Request $request)
    {

        // dd($request->all());
        // $data = validator::make($request->all(), [
        //     'buyer_email' => 'required|email',
        //     'seller_email' => 'required|email',
        //     'amount' => 'required|numeric',
        // ]);

        // if ($data->fails()) {
        //     return response()->json(['error' => $data->errors()], 400);
        // }

        // return response($request->items);

        $data = json_encode($request->all());

        $response = $this->escrowService->createTransaction($data);

        // dd($response->errors);

        if (isset($response->errors)) {
            return response()->json(['errors' => $response->errors], 400);
        }

        if (isset($response->id)) {
            // Transaction::create([
            //     'transaction_id' => $response->id,
            //     'buyer_email' => $data['buyer_email'],
            //     'seller_email' => $data['seller_email'],
            //     'amount' => $data['amount'],
            //     // 'status' => $response->status ?? 'pending',
            //     'status' =>'pending',
            // ]);

            return response()->json(['message' => 'Transaction created successfully!', "response"=>$response], 201);
        }

        return response()->json(['error' => 'Failed to create transaction'], 400);
    }


    //get all registered transactions
    public function getTransaction(Request $request,)
    {
        if ($request->has('transaction_id')) {
            $transactionId = $request->transaction_id;
        }

        $response = $this->escrowService->getTransaction($transactionId ?? null);
        // dd($response->id);
        return $response; 
        if (isset($response->id)) {
            return response()->json($response, 200);
        }

        return response()->json(['error' => 'Transaction not found'], 404);
    }
}
