<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Route;

trait ResourceTrait {
    /**
     * @param string $type
     * @param string $resource
     * @return Application|RedirectResponse|Redirector|void
     */
    public function redirectResourceCheck(string $type, string $resource){
        $check = config('lemonade.redirect.'.$type.'.'.$resource);
        if (!empty($check) && Route::has($type.'.show')) {
            return redirect(route($type.'.show', [$type, $resource]));
        }
    }
}
