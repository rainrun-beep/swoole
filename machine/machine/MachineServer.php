<?php
require_once 'Listens.php';

use \Swoole\Server;

class MachineServer {
    
    private $port = 9505;
    
    private $host = '0.0.0.0';
    
    private $server;
    
    public function __construct() {
        echo swoole_get_local_ip()['eth0'].':'.$this->port."\n";  
        $this->server = new Server($this->host, $this->port);

        //多端口监听
        (new Listens($this->server));
        $this->onEvent();
    }

    public function onEvent() {
        $this->server->on('connect', [$this, 'connect']);
        $this->server->on('receive', [$this, 'receive']);
        $this->server->on('close', [$this, 'close']);
    }

    public function connect($server, $fd) {
        echo "Client Connect.\n";
    }

    public function receive($server, $fd, $from_id, $data) {
        echo '机器接收到消息: '.$data."\n";
        $return = $this->sendToAdmin('172.26.240.108', 9501, $data);

        $server->send($fd, $return);
    }

    public function sendToAdmin($ip, $port, $data) {
        $client = new Swoole\Client(SWOOLE_SOCK_TCP);
        //172.26.240.108 127.0.0.1
        if (!$client->connect($ip, $port, -1)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        $client->send($data);
        $admin_return = $client->recv();
        $client->close();
        return $admin_return;
    }

    public function close($server, $fd) {
        echo "Client: Close\n";
    }

    public function start() {
        echo '启动机器服务'."\n";
        $this->server->start();
    }  
}

(new MachineServer())->start();