<?php

   namespace App\Http\Controllers;

   use App\Models\Transaction;
   use App\Models\Ride;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;
   use Stripe\Charge;
   use Stripe\Stripe;

   class TransactionController extends Controller
   {
       public function pay(Request $request, Ride $ride)
       {
           if ($ride->user_id !== Auth::id() || $ride->status !== 'completed') {
               return response()->json(['message' => 'Cannot process payment'], 403);
           }

           $validated = $request->validate([
               'payment_method' => 'required|string', // e.g., Stripe token
           ]);

           Stripe::setApiKey(config('services.stripe.secret'));

           try {
               $charge = Charge::create([
                   'amount' => $ride->fare * 100, // Convert to cents
                   'currency' => 'usd',
                   'source' => $validated['payment_method'],
                   'description' => 'Payment for ride #' . $ride->id,
               ]);

               $transaction = Transaction::create([
                   'ride_id' => $ride->id,
                   'user_id' => Auth::id(),
                   'amount' => $ride->fare,
                   'status' => 'completed',
                   'payment_method' => 'stripe',
                   'transaction_id' => $charge->id,
               ]);

               return response()->json(['message' => 'Payment successful', 'transaction' => $transaction], 201);
           } catch (\Exception $e) {
               return response()->json(['message' => 'Payment failed', 'error' => $e->getMessage()], 400);
           }
       }
   }