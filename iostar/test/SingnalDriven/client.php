<?php
require_once __DIR__."/../../vendor/autoload.php";
$fp = stream_socket_client("tcp://".swoole_get_local_ip()["eth0"].":9500");
stream_set_blocking($fp, 0); //设置非阻塞

fwrite($fp, "hello world");
fread($fp, 65535);
echo '做其他事情...';
fwrite($fp, "hello world");
fread($fp, 65535);
