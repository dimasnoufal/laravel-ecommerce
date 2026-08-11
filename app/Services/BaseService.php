<?php

namespace App\Services;

abstract class BaseService
{
    /**
     * Standard success response structure for services.
     */
    protected function success($data = [], $message = 'Success')
    {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];
    }

    /**
     * Standard error response structure for services.
     */
    protected function error($message = 'Error', $errors = [])
    {
        return [
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ];
    }
}
