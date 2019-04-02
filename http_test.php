<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2019/4/2
 * Time: 10:04
 */
//使用http协议提供对外服务

use Workerman\Worker;

require __DIR__.'/Workerman-framework/Autoloader.php';

$http_worker = new Worker('http://0.0.0.0:2345');
$http_worker->count = 4;
$http_worker->onMessage = function ($connection,$data)
{
    $connection->send('Hello workerman');
};
Worker::runAll();