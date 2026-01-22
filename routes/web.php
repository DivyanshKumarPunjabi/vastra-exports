<?php

use App\Mail\EnquiryConfirmedMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('get-the-google-reviews', function () {
    $response = Http::get(
        "https://maps.googleapis.com/maps/api/place/details/json",
        [
            'place_id' => env('GOOGLE_PLACE_ID'),
            'fields'   => 'name,rating,reviews',
            'key'      => env('GOOGLE_API_KEY'),
        ]
    );

    $data = $response->json();
    dd($data);
});



Route::get('make-hash', function () {
    $hash = Hash::make('123456');
    dd($hash);
});

Route::get('check-mail', function () {
    try {

        // 🔹 Sample client data
        $clientName  = "Divyansh Kumar Punjabi";
        $clientEmail = "divyansh.punjabi14@gmail.com";

        // 🔹 Email content (as requested)
        $finalMessage = "
        We have successfully received your enquiry, and our team is currently reviewing the details shared by you. One of our representatives will get in touch with you shortly to understand your requirements in detail and guide you through the next steps.

        In the meantime, be prepared with any additional information, references, samples, or questions related to your requirements and our services that you would like to share with us. We will review those materials during a one-on-one meeting. This will help us assist you more efficiently.

        We look forward to connecting with you and exploring how we can collaborate. Enjoy your journey towards becoming a sensational clothing brand.";

        // 🔹 Mail data passed to Mailable
        // $mailData = [
        //     'name'    => $clientName,
        //     'content' => nl2br($finalMessage),
        //     'logo'    => url('/assets/img/evaluare-logo.png'),
        // ];

        $contactData = [
            'first_name'          => $clientName, // optional
            'heading1'            => '',
            'content'             => $finalMessage,
            'image'               => '',
            'white_logo'          => '',
            'content_footnote'    => '', // optional
            'to'                  => $clientEmail, // recipient
            'cc'                  => '', // cc
            // 'cc'                  => '', // cc
            // 'mail_type'           => 'Upload',
        ];

        // 🔹 Send mail (TO CLIENT)
        Mail::to($clientEmail)
            ->send(new EnquiryConfirmedMail($contactData));

        return response()->json([
            'status'  => true,
            'message' => 'Test mail sent successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'error'   => $e->getMessage()
        ]);
    }
});
