<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (!isset($_SERVER['APP_ENV'])) {
    (new Dotenv())->bootEnv(__DIR__ . '/../.env');
}

/** @var string $env */
$env = $_SERVER['APP_ENV'] ?? 'test';
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? true);

$kernel = new Kernel($env, $debug);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
