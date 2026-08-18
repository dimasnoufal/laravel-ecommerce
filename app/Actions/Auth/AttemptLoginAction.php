<?php

namespace App\Actions\Auth;

use App\Actions\BaseAction;
use Illuminate\Support\Facades\Auth;

class AttemptLoginAction extends BaseAction
{
    /**
     * Handle the execution of the action.
     * 
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function execute(...$arguments)
    {
        $credentials = $arguments[0] ?? [];
        $remember = $arguments[1] ?? false;

        return Auth::attempt($credentials, $remember);
    }
}
