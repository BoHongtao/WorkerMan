<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2019/4/8
 * Time: 14:58
 */



//设置Worker子进程启动时的回调函数，每个子进程启动时都会执行


use Workerman\Worker;
require_once __DIR__ . '/../Workerman-framework/Autoloader.php';

$worker = new Worker('websocket://127.0.0.1:8484');
$worker->count = 4;
$worker->onWorkerStart = function($worker)
{
    echo "Worker starting...\n";
};
// 运行worker
Worker::runAll();