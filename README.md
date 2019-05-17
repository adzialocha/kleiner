Kleiner
---

Very simple PHP >=7.1 web framework based on [medoo](https://medoo.in/) and [altorouter](https://altorouter.com/).

## Usage

```php
namespace MyPage;

use Kleiner;

$config = [
    'env' => 'production',
    'db' => [
        'database_type' => 'mysql',
        'database_name' => '',
        'server' => 'localhost',
        'username' => '',
        'password' => ''
    ],
    'assetsPath' => '/dist/',
    'basePath' => '/',
    'baseUrl' => 'https://www.mypage.com'
];

$routes = [
    [
        'path' => '/admin/[pages|users]?',
        'controller' => 'Admin\AdminController',
        'action' => 'index',
    ],
];

$app = new Kleiner(__DIR__ . '/views/', $config);

$app->setupRoutes('MyPage\Controllers\\', $routes);

echo $app->dispatch();
```

```php
<?php

namespace MyPage\Controllers\Admin;

use MyPage\Controllers\AuthController;

class AdminController extends AuthController
{
    public function index ($service, $request, $response)
    {
        $isAuthenticated = $this->isAuthenticated($service, $request, $response);

        $data = [
            'url' => $this->config['baseUrl'],
            'isAuthenticated' => $isAuthenticated,
        ];

        $service->render('admin.php', $data);
    }
}
```

## License

MIT
