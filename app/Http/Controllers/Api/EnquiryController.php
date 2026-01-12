<?php

namespace App\Http\Controllers\Api;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
                    'mobile'     => 'required|string|min:10',
                    'message'    => 'nullable|string',
                ],
                [
                    'first_name.required' => 'First name is required.',
                    'last_name.required' => 'Last name is required.',
                    'email.required' => 'Email is required.',
                    'mobile.required' => 'Mobile Number is required.',
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
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Enquiry submitted successfully.',
                'data' => [
                    $enquiry->makeHidden(['updated_at']),
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
