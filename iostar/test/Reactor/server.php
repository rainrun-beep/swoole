<?php

require_once __DIR__."/../../vendor/autoload.php";

use IoStar\Reactor\Worker;

$server = new Worker('0.0.0.0', 9500);
$server->on('connect', function($server, $client){
    dd($client, "客户端成功建立连接");
});
$server->on('receive', function(Worker $server, $client, $data){
    dd($data, "处理client的数据");
    $server->send($client, "hello i’m is server");
});

$server->on('close', function($server, $client){
    dd($client, "连接断开");
});
$server->start();
