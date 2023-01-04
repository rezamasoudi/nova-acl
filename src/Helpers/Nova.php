<?php

namespace Masoudi\NovaAcl\Helpers;

use Illuminate\Http\Request;

class Nova
{
    public static function isLoginPath(Request $request)
    {
        $novaPath = config('nova.path');
        // check if nova path ends with a slash and remove it
        if (substr($novaPath, -1) === '/') {
            $novaPath = substr($novaPath, 0, -1);
        }

        $requestedPath = $request->GetRequestUri();
        // check if requested path ends with a slash and remove it
        if (substr($requestedPath, -1) === '/') {
            $requestedPath = substr($requestedPath, 0, -1);
        }

        if ($requestedPath === $novaPath . '/login') {
            return true;
        }

        return false;
    }
}