<?php
require_once __DIR__."/../../vendor/autoload.php";

$fp = stream_socket_client("tcp://".swoole_get_local_ip()["eth0"].":9500");
//stream_set_blocking($fp, 0); //设置非阻塞

fwrite($fp, "hello world");

fread($fp, 65535);

echo 'p 其他事情 p<br>';

while(!feof($fp)) {
    sleep(1);
    $read[] = $fp;
    //$fp1, $fp2, $fp3 判断这些链接哪个可读可写
    stream_select($read, $write, $error, 1);
    var_dump($read);
    var_dump(fread($fp, 65535));
}



// if (!$fp) {
//     echo "$errstr($errno)<br />\n";
// }  else {
//     fwrite($fp, "GET /HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
//     while (!feof($fp)) {
//         echo fgets($fp, 1024);
//     }
//     fclose($fp);
// }
