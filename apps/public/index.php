<?php
//include  "./public/index.php";
error_reporting(E_ALL);
ini_set('display_errors', '1');
use App\Kernel;

require_once __DIR__.'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};

