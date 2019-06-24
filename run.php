<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Server\Chat;

require dirname(__DIR__) . '/www/vendor/autoload.php';

/**
 * Load in out environment variables.
 */
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

/**
 * Configure our server.
 */
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    getenv('PORT')
);

$server->run();
