<?php

namespace App\Exceptions;

use Exception;

class UserCreationException extends Exception
{
    public function __construct($message = "User could not be created", $code = 422, Exception $previous = null)
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
            'message' => 'Could not create user',
            'errors' => [
                [
                    'status' => (string) $this->getCode(),
                    'title' => 'User creation failed',
                    'detail' => $this->getMessage(),
                ]
            ]
        ], $this->getCode());
    }
}
