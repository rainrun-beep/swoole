<?php
require_once 'e.php';
// 建立协议服务
$server = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
var_dump($server);

// swoole_event_add()
// Swoole\Event::add($server, function($socket){
//     // 事件类的动作
//     $conn = stream_socket_accept($socket);
//     Swoole\Event::add($conn, function($socket){
//         // 事件类的动作
//         var_dump(fread($socket, 65535));
//         fwrite($socket, 'The local time is ' . date('n/j/Y g:i a') . "\n");
//         Swoole\Event::del($socket);
//         fclose($socket);
//     });
// });
$count = [];
// 设置事件的应用类
$eventBase = new EventBase();
// 定义事件类
$event = new Event($eventBase, $server, Event::WRITE | Event::READ | Event::PERSIST, function($socket) use (&$eventBase, &$count){
    var_dump($socket);
    // 事件类的动作
    $conn = stream_socket_accept($socket);

   (new E($eventBase, $conn, $count))->handler();

});
$count[(int) $server][Event::WRITE | Event::READ | Event::PERSIST] = $server;
$event->add();
// 启动
$eventBase->loop();
