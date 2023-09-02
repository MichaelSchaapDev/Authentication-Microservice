<?php

namespace App\Exceptions;

use Exception;

class CustomAuthenticationException extends Exception
{
    public function __construct($message = "Authentication Error", $code = 401, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function report()
    {
        // Log the exception message
    }

    public function render()
    {
        return response()->json([
            'message' => 'Authentication Error',
            'errors' => [
                [
                    'status' => (string) $this->getCode(),
                    'title' => 'Authentication Error',
                    'detail' => $this->getMessage(),
                ]
            ]
        ], $this->getCode());
    }
}
