<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2019/4/8
 * Time: 17:32
 */

use Workerman\Worker;
require_once __DIR__ . '/../Workerman-framework/Autoloader.php';
require_once __DIR__ . '/../Channel/src/Server.php';
require_once __DIR__ . '/../Channel/src/Client.php';

// 初始化一个Channel服务端
$channel_server = new Channel\Server('0.0.0.0', 2206);

// websocket服务端
$worker = new Worker('websocket://0.0.0.0:4236');
$worker->count=2;
$worker->name = 'pusher';
$worker->onWorkerStart = function($worker)
{
    // Channel客户端连接到Channel服务端
    Channel\Client::connect('127.0.0.1', 2206);
    // 以自己的进程id为事件名称
    $event_name = $worker->id;
    // 订阅worker->id事件并注册事件处理函数
    Channel\Client::on($event_name, function($event_data)use($worker){
        $to_connection_id = $event_data['to_connection_id'];
        $message = $event_data['content'];
        if(!isset($worker->connections[$to_connection_id]))
        {
            echo "connection not exists\n";
            return;
        }
        $to_connection = $worker->connections[$to_connection_id];
        $to_connection->send($message);
    });

    // 订阅广播事件
    $event_name = '广播';
    // 收到广播事件后向当前进程内所有客户端连接发送广播数据
    Channel\Client::on($event_name, function($event_data)use($worker){
        $message = $event_data['content'];
        foreach($worker->connections as $connection)
        {
            $connection->send($message);
        }
    });
};

$worker->onConnect = function($connection)use($worker)
{
    $msg = "workerID:{$worker->id} connectionID:{$connection->id} connected\n";
    echo $msg;
    $connection->send($msg);
};

// 用来处理http请求，向任意客户端推送数据，需要传workerID和connectionID
$http_worker = new Worker('http://0.0.0.0:4237');
$http_worker->name = 'publisher';
$http_worker->onWorkerStart = function()
{
    Channel\Client::connect('127.0.0.1', 2206);
};
$http_worker->onMessage = function($connection, $data)
{
    $connection->send('ok');
    if(empty($_GET['content'])) return;
    // 是向某个worker进程中某个连接推送数据
    if(isset($_GET['to_worker_id']) && isset($_GET['to_connection_id']))
    {
        $event_name = $_GET['to_worker_id'];
        $to_connection_id = $_GET['to_connection_id'];
        $content = $_GET['content'];
        Channel\Client::publish($event_name, array(
            'to_connection_id' => $to_connection_id,
            'content'          => $content
        ));
    }
    // 是全局广播数据
    else
    {
        $event_name = '广播';
        $content = $_GET['content'];
        Channel\Client::publish($event_name, array(
            'content'          => $content
        ));
    }
};

Worker::runAll();
