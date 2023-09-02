<?php

namespace App\Exceptions;

use Exception;

class InternalServerErrorException extends Exception
{
    public function __construct($message = "Database error", $code = 500, Exception $previous = null)
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
            'message' => 'Internal Server Error',
            'errors' => [
                [
                    'status' => (string) $this->getCode(),
                    'title' => 'Internal Server Error',
                    'detail' => $this->getMessage(),
                ]
            ]
        ], $this->getCode());
    }
}
