<?php

namespace App\Traits;

use Exception;

trait ResponseAPI
{
    /**
     * Core of response
     *
     * @param   string          $message
     * @param   array|object    $data
     * @param   integer         $statusCode
     * @param   boolean         $isSuccess
     */
    public function coreResponse($message, $data = null, $statusCode, $isSuccess = true)
    {
        try {
            // Check the params
            if(!$message) return response()->json(['message' => 'Message is required'], 500);

            // Send the response
            if($isSuccess) {
                return response()->json([
                    'message' => $message,
                    'error' => false,
                    'statusCode' => $statusCode > 0 ? $statusCode : 500,
                    'results' => $data
                ], $statusCode > 0 ? $statusCode : 500);
            } else {
                return response()->json([
                    'message' => $message,
                    'error' => true,
                    'statusCode' => $statusCode > 0 ? $statusCode : 500,
                ], 500);
            }
        } catch (Exception $e) {
            if(config('API.debug')) dd($e);
            logger($e);
            return response()->json([
                'message' => $message,
                'error' => true,
                'statusCode' => $statusCode > 0 ? $statusCode : 500,
            ], 500);
        }
    }

    /**
     * Send any success response
     *
     * @param   string          $message
     * @param   array|object    $data
     * @param   integer         $statusCode
     */
    public function success($message, $data, $statusCode = 200)
    {
        return $this->coreResponse($message, $data, $statusCode);
    }

    /**
     * Send any error response
     *
     * @param   string          $message
     * @param   integer         $statusCode
     */
    public function error($message, $statusCode = 500)
    {
        return $this->coreResponse($message, null, $statusCode, false);
    }
}
