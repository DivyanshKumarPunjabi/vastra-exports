<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiBaseController extends Controller
{
    /**
     * Format and return a standard API success or failure response structure.
     *
     * This method prepares a unified response format with a status, message,
     * and any optional data to be merged into the response array.
     *
     * Note: This method returns an array. Wrap it using `response()->json(...)`
     * in your controller methods to comply with expected JsonResponse return type.
     *
     * @param  bool   $result   Indicates success (true) or failure (false)
     * @param  string $message  The message to be included in the response
     * @param  array  $data     (Optional) Additional data to include in the response
     * @return array
     */
    public function sendResponse($result, $message, $data = [])
    {
        $res = [
            'status' => $result,
            'message' => $message,
        ];

        if (!empty($data)) {
            $res = array_merge($res, $data);
        }
        return $res;
    }

    /**
     * Generate a standard error response for exceptions and log handling.
     *
     * This method formats the catch block response structure with a default error message
     * and includes the original exception message for debugging (can be omitted in production).
     *
     * Note: This method returns an array. Ensure it is wrapped using `response()->json(...)`
     * in your controller methods to return a proper JsonResponse.
     *
     * @param  string $error  The exception message or error string to include
     * @return array
     */
    public function sendCatchLog($error)
    {

        $res = [
            'status' => false,
            'message' => 'Some error occurred.Please Try again later',
            'error' => $error
        ];

        return $res;
    }
}
