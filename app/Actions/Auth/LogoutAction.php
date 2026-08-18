<?php

namespace App\Actions\Auth;

use App\Actions\BaseAction;
use Illuminate\Support\Facades\Auth;

class LogoutAction extends BaseAction
{
    /**
     * Handle the execution of the action.
     * 
     * @return void
     */
    public function execute(...$arguments)
    {
        Auth::logout();
    }
}
