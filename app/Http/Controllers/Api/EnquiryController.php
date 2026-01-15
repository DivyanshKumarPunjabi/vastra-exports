<?php

namespace App\Http\Controllers\Api;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\EnquiryConfirmedMail;

class EnquiryController extends ApiBaseController
{
    public function submitEnquiry(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required|string|max:255',
                    'last_name'  => 'required|string|max:255',
                    'email'      => 'required|email|max:255',
                    'mobile'     => 'required|min:10',
                    'message'    => 'nullable|string',
                    'country_code' => 'required|string'
                ],
                [
                    'first_name.required' => 'First name is required.',
                    'last_name.required' => 'Last name is required.',
                    'email.required' => 'Email is required.',
                    'mobile.required' => 'Mobile Number is required.',
                    'country_code.required' => 'Country Code is required.',
                    'email.email' => 'Please enter a valid email address.',
                    'mobile.min'  => 'Mobile number must be 10 characters.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            $validated = $validator->validated();
            $enquiry = Enquiry::create($validated);

            $clientName = $validated['first_name'] . ' ' . $validated['last_name'];
            $clientEmail = $validated['email'];

            $finalMessage = "We have successfully received your enquiry, and our team is currently reviewing the details shared by you. One of our representatives will get in touch with you shortly to understand your requirements in detail and guide you through the next steps.

            In the meantime, be prepared with any additional information, references, samples, or questions related to your requirements and our services that you would like to share with us. We will review your concern during a one-on-one meeting. This will help us assist you more efficiently.
            
            We look forward to connecting with you and exploring how we can collaborate. Enjoy your journey towards becoming a sensational clothing brand.";

            $contactData = [
                'first_name'          => $clientName, // optional
                'heading1'            => '',
                'content'             => $finalMessage,
                'image'               => '',
                'white_logo'          => '',
                'content_footnote'    => '', // optional
                'to'                  => $clientEmail, // recipient
                'cc'                  => '', // cc
            ];

            Mail::to($clientEmail)
                ->send(new EnquiryConfirmedMail($contactData));
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Enquiry submitted successfully.',
                'data' => [
                    $enquiry->makeHidden(['updated_at', 'id', 'created_at']),
                ],
            ], 200);
        } catch (Exception $e) {
            Log::error('Enquiry submit error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
