<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403, 'Super admin access required.');
    }
}
