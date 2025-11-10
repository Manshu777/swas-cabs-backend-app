<?php

namespace App\Http\Controllers\Users;

use App\Events\RideCreated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\BookRide;

class PaymentController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|exists:book_rides,id',
            'amount' => 'required|numeric',
        ]);

        $ride = BookRide::find($request->ride_id);

        $orderData = [
            'receipt'         => 'ride_'.$ride->id,
            'amount'          => $request->amount * 100, // in paise
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $razorpayOrder = $this->razorpay->order->create($orderData);

        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'amount' => $request->amount,
            'currency' => 'INR',
            'key' => config('services.razorpay.key'),
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'ride_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        $generated_signature = hash_hmac('sha256',
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            config('services.razorpay.secret')
        );

        if ($generated_signature === $request->razorpay_signature) {
            $ride = BookRide::find($request->ride_id);
            $ride->update([
                'payment_status' => 'paid',
                'payment_method' => 'razorpay',
                'status' => 'confirmed'
            ]);

            event(new RideCreated($ride));

            return response()->json(['success' => true, 'message' => 'Payment verified']);
        }

        return response()->json(['success' => false, 'message' => 'Payment failed'], 400);
    }
}