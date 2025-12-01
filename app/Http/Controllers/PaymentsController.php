<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
   {
       /**
        * Show the payment form for an adoption application.
        */
       public function create(AdoptionApplication $application)
       {
           // Ensure the user owns the application
           if ($application->user_id !== auth()->id()) {
               abort(403, 'Unauthorized');
           }

           // Load related pet data
           $application->load('pet');

           return view('payments.create', compact('application')); // Create this view (see below)
       }

       /**
        * Process the payment for an adoption application.
        */
       public function store(Request $request, AdoptionApplication $application)
       {
           // Ensure the user owns the application
           if ($application->user_id !== auth()->id()) {
               abort(403, 'Unauthorized');
           }

           $validated = $request->validate([
               'stripeToken' => 'required|string', // Token from Stripe.js
               'amount' => 'required|numeric|min:0', // Adoption fee amount
           ]);

           try {
               // Set Stripe secret key
               Stripe::setApiKey(env('STRIPE_SECRET'));

               // Create a charge
               $charge = Charge::create([
                   'amount' => $validated['amount'] * 100, // Convert to cents
                   'currency' => 'usd', // Change to 'php' if using Philippine Peso
                   'description' => 'Adoption fee for ' . $application->pet->name,
                   'source' => $validated['stripeToken'],
               ]);

               // Mark application as paid (assuming you have a 'paid' column in adoption_applications table)
               $application->update(['paid' => true]);

               return redirect()->route('applications.show', $application)
                   ->with('success', 'Payment successful! Your adoption application is now complete.');

           } catch (\Exception $e) {
               return back()->with('error', 'Payment failed: ' . $e->getMessage());
           }
       }
   }
   