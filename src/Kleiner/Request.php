<?php

namespace Kleiner;

class Request
{
    protected $config;

    protected $route;

    public function __construct ($config)
    {
        $this->config = $config;
    }

    public function setRoute($match) {
        $this->route = $match;
    }

    public function getRoute ()
    {
        return $this->route;
    }

    public function getParams ()
    {
        $json = [];

        $contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

        if (strpos($contentType, 'application/json') !== false){
            $content = trim(file_get_contents('php://input'));
            $decoded = json_decode($content, true);

            if (is_array($decoded)){
                $json = $decoded;
            }
        }

        return array_merge($_GET, $_POST, $json);
    }

    public function getFiles ()
    {
        return $_FILES;
    }

    public function getCookies ()
    {
        return $_COOKIE;
    }
}
