<?php

namespace App\Actions;

use Exception;
use Illuminate\Support\Facades\Log;

abstract class BaseAction
{
    /**
     * Handle the execution of the action.
     * All extending classes must implement this method.
     */
    abstract public function execute();

    /**
     * Run the action with standard exception logging.
     * Overriding execute is required, but you can call run() to automatically catch and log.
     */
    public function run(...$arguments)
    {
        try {
            return $this->execute(...$arguments);
        } catch (Exception $e) {
            Log::error(static::class . ' failed: ' . $e->getMessage(), [
                'arguments' => $arguments,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
