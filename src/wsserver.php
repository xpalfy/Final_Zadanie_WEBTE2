<?php

use Workerman\Worker;
use Workerman\Connection\TcpConnection;
require_once __DIR__ . '/vendor/autoload.php';

$ws_worker = new Worker("websocket://0.0.0.0:8282");

$ws_worker->count = 1;
$ws_worker->onConnect = function($connection) {
    echo "New connection\n";
};

$ws_worker->onMessage = function($connection, $data) use ($ws_worker) {
    // Broadcast data to all connected clients
    foreach ($ws_worker->connections as $client_connection) {
        $client_connection->send($data);
    }
};

$ws_worker->onClose = function($connection) {
    echo "Connection closed\n";
};

// Run all workers
Worker::runAll();
?>
