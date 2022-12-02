<?php

use App\Middlewares\MyMiddleware;
use IconicCodes\LightRouter\LightRouter;

include './vendor/autoload.php';

$router = new LightRouter();
$router->addRoute('GET', '/', function() {
    echo '/';
},
[
    MyMiddleware::class
]);

$router->run();