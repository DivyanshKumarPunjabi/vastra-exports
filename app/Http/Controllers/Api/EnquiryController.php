<?php

namespace App\Http\Controllers\Api;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class EnquiryController extends ApiBaseController
{
    public function submitEnquiry(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name'  => 'nullable|string|max:255',
                'email'      => 'nullable|email|max:255',
                'mobile'     => 'nullable|string|max:20',
                'message'    => 'nullable|string',
            ]);

            $enquiry = Enquiry::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry submitted successfully.',
                'data' => $enquiry->makeHidden(['created_at', 'updated_at']),
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
