<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DonationController extends Controller
{
  public function __construct(){
    // Set your Merchant Server Key
    \Midtrans\Config::$serverKey = config('services.midtrans.serverKey');
    // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
    \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
    // Set sanitization on (default)
    \Midtrans\Config::$isSanitized = config('services.midtrans.isSanitized');
    // Set 3DS transaction for credit card to true
    \Midtrans\Config::$is3ds = config('services.midtrans.is3ds');
  }

  public function index () {
    $donations = Donation::all();
    return view('index', compact('donations'));
  }

  public function create () {
      return view('donation');
  }

  public function store (Request $request) {
    $response = null;

    DB::transaction(function() use ($request, &$response){
      $donation = Donation::create([
        'donation_code' => Str::ulid(),
        'donor_name' => $request->donor_name,
        'donor_email' => $request->donor_email,
        'donation_type' => $request->donation_type,
        'amount' => $request->amount,
        'note' => $request->note,
      ]);
      $payload = [
        "transaction_details" => [
          "order_id"=> $donation->donation_code,
          "gross_amount"=> $donation->amount
        ],
        "customer_details" => [
          'donor_name' => $donation->donor_name,
          'donor_email' => $donation->donor_email,
        ],
        "item_details" => [
          [
            "id" => $donation->type,
            "price" => $donation->amount,
            "quantity" => 1,
            "name" => ucwords(str_replace('-', ' ', $donation->donation_type)),
          ]
        ],
      ];

      $snapToken = \Midtrans\Snap::getSnapToken($payload);
      $donation->snap_token = $snapToken;
      $donation->save();
      $response = $snapToken;
    });
    return response()->json([
      'snap_token' => $response,
    ]);
  }

  public function notification(){
    $notif = new \Midtrans\Notification();
    DB::transaction(function () use ($notif){
      $transactionStatus = $notif->transaction_status;
      $paymenType = $notif->transaction_notif;
      $orderId = $notif->order_id;
      $fraudStatus = $notif->fraud_status;
      $donation = Donation::where('donation_code', $notif->order_id)->first();
      switch ($transactionStatus) {
        case 'capture':
            switch ($paymenType) {
              case 'credit_card':
                $donation->setStatusPending();
                break;
              default:
                $donation->setStatusSuccess();
                break;
            }
        case 'settlement':
          $donation->setStatusSuccess();
          break;

        case 'pending':
          $donation->setStatusPending();
          break;

        case 'deny':
          $donation->setStatusFailed();
          break;

        case 'expire':
          $donation->setStatusExpired();
          break;

        case 'cancel':
          $donation->setStatusFailed();
          break;

        default:
          $donation->setStatusFailed();
          break;
      } 
    });
    return;
  }
}
