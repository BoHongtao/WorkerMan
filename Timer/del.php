<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2019/4/8
 * Time: 15:30
 */


use \Workerman\Worker;
use \Workerman\Lib\Timer;
require_once __DIR__ . '/../Workerman-framework/Autoloader.php';

$task = new Worker();
// 开启多少个进程运行定时任务，注意业务是否在多进程有并发问题
$task->count = 1;
$task->onWorkerStart = function($task)
{
    // 每2秒执行一次
    $time_interval = 2;
    $timer_id = Timer::add($time_interval, function() use ($task,&$timer_id)
    {
        static $i = 0;
        echo $task->id."task run\n";
        $i++;
        if($i === 10){
            Timer::del($timer_id);
        }
    });
};
// 运行worker
Worker::runAll();