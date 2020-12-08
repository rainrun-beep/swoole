<?php

require_once __DIR__."/../../vendor/autoload.php";
// 连接服务端
$fp = stream_socket_client("tcp://192.168.169.100:9500");
fwrite($fp, " is client");
dd(fread($fp, 65535));
fclose($fp);
