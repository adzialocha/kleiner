<?php

namespace Kleiner;

class Response
{
    protected $config;

    public function __construct ($config)
    {
        $this->config = $config;
    }

    public function setResponseCode ($code)
    {
        http_response_code($code);
    }

    public function redirect ($url, $code = 302)
    {
        $this->setResponseCode($code);

        header('Location: ' . $url);

        die();
    }

    public function json ($data)
    {
        header('Content-Type: application/json');

        echo (string) json_encode($data);

        die();
    }

    public function setCookie ($name, $value, $expiry)
    {
        $isSecure = $this->config['env'] == 'production';

        setcookie($name, $value, $expiry, '/', null, $isSecure);
    }

    public function removeCookie ($name)
    {
        unset($_COOKIE[$name]);
    }
}
