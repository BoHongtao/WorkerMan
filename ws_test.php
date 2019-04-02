<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2019/4/2
 * Time: 10:26
 */


use Workerman\Worker;

require __DIR__.'/Workerman-framework/Autoloader.php';

$ws_worker = new Worker("websocket://0.0.0.0:2000");

$ws_worker->count = 1;
$ws_worker->onMessage = function($connection, $data)
{
    $connection->send('hello ' . $data);
};
Worker::runAll();
