<?php
//建立协议服务
$socket = stream_socket_server("tcp://0.0.0.0:9503", $errno, $errstr);  

/**
 * 使用stream_socket_server中会新建立一个socket1, 如果有用户来连接服务端, stream_socket_accept监听socket1就会有消息, 然后stream_socket_accept就会新建立一个socket2用来服务端和客户端交换消息, socket1只是负责监听是否有新用户产生
 */
if (!$socket) {
  echo "$errstr ($errno)<br />\n";
} else {
  //创建服务和客户连接的socket
  //stream_socket_accept代码只会监听一次 
  while(true) {
    $conn = stream_socket_accept($socket);
    if ($conn) {
      fwrite($conn, 'The local time is ' . date('n/j/Y g:i a') . "\n");
      while (!feof($conn)) {
           echo fgets($conn, 1024);
      }
      fclose($conn);
    }
  }
}
?>
