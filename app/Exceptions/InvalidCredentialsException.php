<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct($message = "Invalid credentials", $code = 401, Exception $previous = null)
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
            'message' => 'The given credentials were invalid.',
            'errors' => [
                [
                    'status' => (string) $this->getCode(),
                    'title' => 'Invalid email or password',
                    'detail' => 'The email and password you entered did not match our records. Please double-check and try again.',
                ]
            ]
        ], 401);
    }
}
