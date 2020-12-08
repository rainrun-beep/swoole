<?php
namespace iostar\SingnalDriven;
use iostar\WorkerBase;

class Worker extends WorkerBase {
    protected function accept() {
        while(true) {
            //接收创建的服务器套接字连接
            //为什么同一个连接发送两次消息只能接收到第一次的消息
            //因为代码会往下走, stream_socket_accept监听12socket的连接之后, while代码执行完了之后，stream_socket_accept又会监听新的连接
            $conn = stream_socket_accept($this->server, -1); 
            if(!empty($conn)) {
                //触发建立连接事件
                $this->events['connect']($this, $conn);
            }
            $this->conn = $conn;
            //安装信号处理器
            pcntl_signal(SIGIO, $this->sigHandler($conn));
            //向当前进程发送SIGIO信号
            posix_kill(posix_getpid(), SIGIO);
            //调用等待信号的处理器
            pcntl_signal_dispatch();
        }
    }

    //信号处理函数
    public function sigHandler($conn) {
        return function ($sig) use ($conn) {
            switch ($sig) {
                case SIGIO:
                    $this->sendMessage($conn);
                    break;
            }
        };
    }

     //发送消息
    protected function sendMessage($conn) {
        $data = fread($conn, 65535);

        if ('' === $data || false === $data) {
            $this->checkConn($data, $conn);
        } else {
            $this->events['receive']($this, $conn, $data);
        }
    }

    //校验信息
    protected function checkConn($data, $conn) {
        if (\strlen($data) === 0) {

            if (\get_resource_type($conn) !== "Unknown") {
                $this->close($conn);
            }

            //断开连接
            \call_user_func($this->events['close'], $this, $conn);
            unset($this->sockets[(int) $conn]);
        }
    }

}