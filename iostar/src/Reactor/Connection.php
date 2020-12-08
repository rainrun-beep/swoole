<?php
namespace IoStar\Reactor;

/**
 * 设置监听客户端和服务端生成的socket触发的回调事件
 */
class Connection
{
     protected $conn;
     protected $server;

     public function __construct($conn, $server)
     {
         $this->conn = $conn;
         $this->server = $server;
     }

     public function handler()
     {
         Reactor::getInstance()->add($this->conn, Reactor::EVENT, $this->sendMessage());
     }

     public function sendMessage()
     {
        return function($conn){
            dd("接收服务的信息");
            // 接收服务的信息
            $buffer = fread($conn, 65535);

            if ('' === $buffer || false === $buffer) {
                // 校验是否断开连接
                $this->checkConn($buffer, $conn);
            } else {
                $this->server->event['receive']($this->server, $conn, $buffer);
            }
        };
     }

     /**
      * 校验连接状态
      * @method closeConn
      * @param  socket    $conn 连接信息
      */
     public function checkConn($buffer, $conn)
     {
         if (strlen($buffer) === 0) {
             if (!get_resource_type($conn) == "Unknown") {
                 // 关闭连接
                 $this->close($conn);
             }
             $this->server->event['close']($this->server, $conn);
         }
         Reactor::getInstance()->del($conn, Reactor::EVENT);
     }


 }
