<?php
require_once __DIR__."/../../vendor/autoload.php";
dd(swoole_get_local_ip()["eth0"]);
$fp = stream_socket_client("tcp://".swoole_get_local_ip()["eth0"].":9500");
fwrite($fp, 'hello world');
dd(fread($fp, 65535));

fwrite($fp, 'hello world');
dd(fread($fp, 65535));

fwrite($fp, 'hello world');
dd(fread($fp, 65535));
