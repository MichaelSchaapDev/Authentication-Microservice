<?php

namespace App\Exceptions;

use Exception;

class AccessTokenExtractionException extends Exception
{
    public function __construct($message = "Could not extract access token", $code = 401, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        // Log the exception message
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'message' => 'Could not extract access token',
            'errors' => [
                [
                    'status' => (string) $this->getCode(),
                    'title' => 'Access Token cannot be extracted from the JSON response',
                    'detail' => $this->getMessage(),
                ]
            ]
        ], 401);
    }
}
