<?php
namespace IoStar\Reactor;

use IoStar\WorkerBase;
use \Event as Event;
use \EventBase as EventBase;

// io事件模型
/**
 * 根据io五大网络模式的不同设置不同的接收函数
 */
class Worker extends WorkerBase
{

    public $events = [

    ];
    public function accept()
    {
        Reactor::getInstance()->add($this->server, Reactor::EVENT, $this->createConn());
        Reactor::getInstance()->run();
    }

    public function createConn()
    {
        return function($socket){
            // dd();
            $conn = stream_socket_accept($socket);
            if (!empty($conn) && get_resource_type($conn) == "stream") {
                 //触发事件的连接的回调
                 $this->event['connect']($this, $conn);
            }
            (new Connection($conn, $this))->handler();
        };
    }
}
