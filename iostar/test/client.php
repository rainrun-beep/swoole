<?php
//连接服务端的socket
$fp = stream_socket_client("tcp://".swoole_get_local_ip()["eth0"].":9503");
if (!$fp) {
    echo "$errstr($errno)<br />\n";
}  else {
    fwrite($fp, "GET /HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
    while (!feof($fp)) {
        echo fgets($fp, 1024);
    }
    fclose($fp);
}

