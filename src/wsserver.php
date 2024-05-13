<?php

use Workerman\Worker;
require_once __DIR__ . '/vendor/autoload.php';

$ws_worker = new Worker("websocket://0.0.0.0:8282");

$ws_worker->onConnect = function($connection) {
    echo "New connection\n";
};

$ws_worker->onMessage = function($connection, $data) {
    $connection->send('Hello ' . $data);
};

$ws_worker->onClose = function($connection) {
    echo "Connection closed\n";
};

// Run all workers
Worker::runAll();
