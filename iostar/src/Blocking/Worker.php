<?php
namespace iostar\Blocking;
use iostar\WorkerBase;

class Worker extends WorkerBase {
    protected function accept() {
        while(true) {
            $conn = stream_socket_accept($this->server, -1); //接收创建的服务器套接字连接
            if(!empty($conn)) {
                //触发建立连接事件
                $this->events['connect']($this, $conn);

                //接收服务的信息
                $data = fread($conn, 65535); //获取客户端发送的数据

                //触发接收事件
                $this->events['receive']($this, $conn, $data);

                //缺乏心跳检测


                if (!empty($conn) && \get_resource_type($conn) == "Unknown") {
                    $this->events['close']($this, $conn);
                }
            }

        }
    }

}
