<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // Polyfill authorizeResource to avoid environment issues
    protected function authorizeResource(string $model, string $parameter, array $options = []): void
    {
        $abilityMap = [
            'index' => 'viewAny',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
        ];

        foreach ($abilityMap as $method => $ability) {
            $argument = in_array($ability, ['viewAny','create'], true) ? $model : $parameter;
            $this->middleware("can:$ability,$argument")->only($method);
        }
    }
}
