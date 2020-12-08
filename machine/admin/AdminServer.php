<?php

use \Swoole\Server;

class AdminServer {
    
    private $port = 9501;
    
    private $host = '172.26.240.108';
    
    private $server;
    
    public function __construct() {
        echo swoole_get_local_ip()['eth0'].':'.$this->port."\n";  
        $this->server = new Server($this->host, $this->port);

        $this->onEvent();
    }

    public function onEvent() {
        $this->server->on('connect', [$this, 'connect']);
        $this->server->on('receive', [$this, 'receive']);
        $this->server->on('receive', [$this, 'receive']);
    }

    public function connect($server, $fd) {
        echo "Client Connect.\n";
    }

    public function receive($server, $fd, $from_id, $data) {
        echo 'admin接收到消息'.$data."\n";
        echo 'admin处理数据....'."\n";
        $data = json_decode($data, true);
        $this->{$data['method']}($server, $fd, $from_id, $data); //不加{}报错
        echo 'admin处理完成....'."\n";
    }

    public function machineinfo($server, $fd, $from_id, $data) {
        //处理数据

        //返回
        $server->send($fd, json_encode(['code'=>200, 'msg'=>'return info ok'])."\n");

    }

    public function machinestop($server, $fd, $from_id, $data) {
        echo "admin去停止机器\n";
        $return = $this->sendToMachine('127.0.0.1', 9556, json_encode($data));
        //处理其他事情


        echo "机器已经停止\n";
        $server->send($fd, json_encode(['code'=>200, 'msg'=>'machine game over'])."\n");
    }

    public function sendToMachine($ip, $port, $data) {
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
        echo '启动后台服务';
        $this->server->start();
    }  
}

(new AdminServer())->start();
