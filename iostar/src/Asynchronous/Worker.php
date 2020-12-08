<?php
namespace IoStar\Async;

use IoStar\WorkerBase;
use \EventBase;
use \Event;
/**
 * 阻塞模型
 */
class Worker extends WorkerBase
{
    public function __construct($host, $port)
    {
        parent::__construct($host, $port);
        stream_set_blocking($this->server, 0);
    }

    protected function accept()
    {
        $count = [];
        // 设置事件的应用类
        $eventBase = new EventBase();
        // 定义事件类
        $event = new Event($eventBase, $this->server, Event::WRITE | Event::READ | Event::PERSIST, function($socket) use (&$eventBase, &$count){
            // 事件类的动作
            $conn = stream_socket_accept($socket);
            if (!empty($conn)) {
                // 触发建立连接事件
                $this->events['connect']($this, $conn);
            }
            // 处理通信
            (new Events($eventBase, $conn))->handler($this, $count);
        });
        $count[(int) $this->server][Event::WRITE | Event::READ | Event::PERSIST] = $this->server;
        $event->add();
        // 启动
        $eventBase->loop();
    }

    // 发送信息
    public function sendMessage($conn)
    {
        // 接收服务的信息
        $data = fread($conn, 65535);
        // \strlen($data) === 0;
        // dd(strlen($data), '接收服务的信息');
        if ('' === $data || false === $data) {
            // $this->checkConn($data, $conn);
        } else {
            $this->events['receive']($this, $conn, $data);
        }
    }
    // 校验连接
    protected function checkConn($buffer, $conn)
    {
        if (\strlen($buffer) === 0) {
            if (! \get_resource_type($conn) == "Unknown"){
                // 断开连接
                $this->close($conn);
            }
            \call_user_func($this->events['close'], $this, $conn );
            unset($this->sockets[(int) $conn]);
        }
    }
}
