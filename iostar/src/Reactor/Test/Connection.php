<?php
namespace IoStar\Reactor;

class Connection
{
     protected $conn;

     protected $worker;

     public function __construct($conn, $worker)
     {
         $this->conn = $conn;
         $this->worker = $worker;
     }

     public function handler()
     {
         Reactor::getInstance()->add($this->conn, Reactor::EVENT, $this->sendMessage());
     }

     public function sendMessage()
     {
        return function($conn){
            dd("接收服务的信息");
            sleep(4);
            // 接收服务的信息
            $buffer = fread($conn, 65535);
            dd($buffer);
            sleep(3);
            if ('' === $buffer || false === $buffer) {
                // 校验是否断开连接
                $this->checkConn($buffer, $conn);
            } else {
                $this->worker->event['receive']($this->worker, $conn, $buffer);
                Reactor::getInstance()->del($conn, Reactor::EVENT);
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
                 $this->worker->close($conn);
             }
             $this->worker->event['close']($this->worker, $conn);
         }
         dd("校验连接状态");
         sleep(3);
         Reactor::getInstance()->del($conn, Reactor::EVENT);
     }
 }
