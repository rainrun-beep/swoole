<?php
require_once __DIR__."/../../vendor/autoload.php";

$fp = stream_socket_client("tcp://".swoole_get_local_ip()["eth0"].":9500"); //打开internet或unix套接字连接
stream_set_blocking($fp, 0); //设置非阻塞

fwrite($fp, "hello world"); //给服务器端发送数据

fread($fp, 65535);  //读取服务端的数据

echo 'p 其他事情 p<br>';

while(!feof($fp)) { //如果没有读取完数据
    sleep(1);
    $read[] = $fp;
    //$fp1, $fp2, $fp3 判断这些链接哪个可读可写
    stream_select($read, $write, $error, 1);  //获取当前连接unix套接字连接的所有服务,是否可读写
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