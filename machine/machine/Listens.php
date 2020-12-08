<?php

use \Swoole\Server;

class Listens {
    
    private $port = 9556;
    
    private $host = '127.0.0.1';
    
    private $server;

    private $listen;
    
    public function __construct($server) {
        $this->server = $server;
        
        echo $this->host.':'.$this->port."\n";  
        
        $this->listen = $this->server->listen($this->host, $this->port, SWOOLE_SOCK_TCP);

        //多端口监听

        $this->onEvent();
    }

    public function onEvent() {
        $this->listen->on('connect', [$this, 'connect']);
        $this->listen->on('receive', [$this, 'receive']);
        $this->listen->on('close', [$this, 'close']);
    }

    public function connect($server, $fd) {
        echo "机器人监听 Client Connect.\n";
    }

    public function receive($server, $fd, $from_id, $data) {
        echo '接收到指令处理后端操作'."\n";
        $data = json_decode($data, true);
        if ($data['code'] === 4) {
            $this->server->shutdown();
        } else {
            $server->send($fd, $data);
        }
    }

    public function close($server, $fd) {
        echo "机器人监听 Client: Close\n";
    } 
}
