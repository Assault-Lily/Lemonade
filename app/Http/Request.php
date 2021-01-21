<?php

namespace App\Http;

class Request extends \Illuminate\Http\Request
{
    protected function prepareRequestUri()
    {
        if ((int) $this->server->get('REDIRECT_STATUS', '200') >= 400 && $this->server->has('REDIRECT_URL')) {
            $requestUri = $this->server->get('REDIRECT_URL');
            $this->server->set('REQUEST_URI', $requestUri);
            return $requestUri;
        }

        return parent::prepareRequestUri();
    }
}
