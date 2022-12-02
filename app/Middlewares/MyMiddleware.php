<?php
namespace App\Middlewares;

use IconicCodes\LightRouter\IMiddleware;

class MyMiddleware extends IMiddleware {
    public function handle() {
        echo 'Hi';
        return true;
    }
}
