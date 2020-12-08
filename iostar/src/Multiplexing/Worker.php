<?php
namespace iostar\Multiplexing;
use iostar\WorkerBase;

class Worker extends WorkerBase {
    private $sockets;

    public function __construct($host, $port) {
        parent::__construct($host, $port);
        //设置不阻塞服务
        \stream_set_blocking($this->server, 0);

        //记录服务的socket [int => source]
        $this->sockets[(int) $this->server] = $this->server;
    }


    protected function accept() {
        while(true) {
            $reads = $this->sockets;
            //检测socket集合中socket资源的状态
            \stream_select($reads, $write, $e, 60);
            sleep(1);
            foreach($reads as $key => $socket) {
                //dd($reads, '$reads');
                //dd($socket, '$socket');
                //dd($this->server, '$this->server');
                //dd($keys, '$keys');
                //如果当前可读的socket连接中有server的话,创建新连接
                if ($socket == $this->server) {
                    //新的连接
                    $conn = $this->createConn();
                    if ($conn) {
                        $this->sockets[(int) $conn] = $conn;
                    } else {
                        dd("连接建立不成功");
                    }
                } else {
                    //进行消息通信
                    dd('进行消息通信');
                    $this->sendMessage($socket);
                }
            }
        }   
    }

    //创建连接
    protected function createConn() {
        $conn = stream_socket_accept($this->server);
        if (!empty($conn)) {
            $this->events['connect']($this, $conn);
            return $conn;
        }
        return null;
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
