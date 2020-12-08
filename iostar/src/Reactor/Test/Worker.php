<?php
namespace IoStar\Reactor;

use IoStar\WorkerBase;
use \EventBase;
use \Event;
/**
 * 阻塞模型
 */
class Worker extends WorkerBase
{

    public $events = [

    ];

    public function __construct($host, $port)
    {
        parent::__construct($host, $port);
        stream_set_blocking($this->server, 0);
    }

    protected function accept()
    {
        Reactor::getInstance()->add($this->server, Reactor::EVENT, $this->createConn());
        Reactor::getInstance()->run();
    }

    public function createConn()
    {
        return function($socket){
            // 事件类的动作
            $conn = stream_socket_accept($socket);

            if (!empty($conn)) {
                // 触发建立连接事件
                $this->events['connect']($this, $conn);
            }
            // 处理通信
            dd("事件类的动作");
            (new Connection($conn, $this))->handler();
            dd("Connection");
            // sleep(4);
        };
    }

}
