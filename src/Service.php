<?php

namespace Kleiner;

class Service
{
    protected $layout;

    protected $view;

    protected $config;

    protected $viewsBasePath;

    public $data;

    public function __construct ($config)
    {
        $this->config = $config;

        $this->data = [];
    }

    public function setViewsBasePath($basePath)
    {
        $this->viewsBasePath = $basePath;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function yield ()
    {
        require $this->viewsBasePath . $this->view;
    }

    public function render ($view, array $data = [])
    {
        $original_view = $this->view;

        if (!empty($data)) {
            $this->data = array_merge($this->data, $data);
        }

        $this->view = $view;

        if (null === $this->layout) {
            $this->yield();
        } else {
            require $this->viewsBasePath . $this->layout;
        }

        $this->view = $original_view;
    }

    public function partial ($view, array $data = [])
    {
        $layout = $this->layout;
        $this->layout = null;
        $this->render($view, $data);
        $this->layout = $layout;
    }

    public function asset ($str)
    {
        // Detect folder based on suffix
        if (strpos($str, '.js')) {
            $folder = 'scripts/';
        } elseif (strpos($str, '.css')) {
            $folder = 'styles/';
        } elseif (preg_match("#\.(jpg|ico|jpeg|gif|png)$# i", $str)) {
            $folder = 'images/';
        } else {
            $folder = '';
        }

        // Build the file path for the browser
        $path = implode('', [
            $this->config['basePath'],
            ltrim($this->config['assetsPath'], '/'),
            $folder,
            str_replace('/', '', $str),
        ]);

        // Add a version to the filepath by reading its modified timestamp
        $absolutePath = getcwd() . $path;
        $modifiedTime = '';

        if (file_exists($absolutePath)) {
            $modifiedTime = '?v=' . filemtime($absolutePath);
        }

        return $path . $modifiedTime;
    }

    public function url ($str)
    {
        $path = [
            $this->config['basePath'],
            ltrim($str, '/')
        ];

        return implode('', $path);
    }
}
