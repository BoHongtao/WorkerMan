<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2019/4/2
 * Time: 10:34
 */

use Workerman\Worker;
require __DIR__.'/Workerman-framework/Autoloader.php';

$tcp_worker = new Worker("tcp://0.0.0.0:2347");

$tcp_worker->count = 4;

$tcp_worker->onMessage = function($connection, $data)
{
    $connection->send('hello ' . $data);
};

Worker::runAll();