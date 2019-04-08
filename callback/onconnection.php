<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2019/4/8
 * Time: 15:06
 */


//连接(三次握手后)只会触发一次onConnect回调。此外还有onMessage（接受到消息）、
//onClose（断开连接）、onBufferFull（缓冲区满了）等函数，用的时候再查手册
use Workerman\Worker;
require_once __DIR__ . '/../Workerman-framework/Autoloader.php';

$worker = new Worker('websocket://127.0.0.1:8484');
$worker->onConnect = function($connection)
{
    echo "new connection from ip " . $connection->getRemoteIp() . "\n";
};
// 运行worker
Worker::runAll();