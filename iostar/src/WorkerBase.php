<?php
namespace iostar;

abstract class WorkerBase {

    protected $events = [];

    protected $config;

    protected $server;

    protected $type = 'tcp';

    public function __construct($host, $port) {
        $this->server = stream_socket_server($this->type."://".$host.":".$port);
        dd("tcp://".swoole_get_local_ip()['ens33'].$host.":".$port, '启动swoole服务');
    }

    /**
     * 服务开始
     */
    public function start() {
        dd($this->events, '开始swoole服务');
        $this->check();
        $this->accept();
    }

    /**
     * 服务关闭
     */
    public function close($client) {
        
        fclose($client);
        dd($client);

    }

    /**
     * 绑定事件
     */
    public function on($event, $call) {
        $this->events[\strtolower($event)] = $call;
        
    }

    /**
     * 发送内容
     */
    public function send($client, $data) {
        \fwrite($client, $data);
        
    }

    /**
     * 设置服务配置
     */
    public function set() {}

    /**
     * 建立连接
     * 不同的模型实现方式不一样
     */
    protected abstract function accept();

    /**
     * 校验是否注册事件与事件的类型
     */
    public function check() {
        if($this->type == 'tcp') {
            if (empty($this->events['receive']) || !$this->events['receive'] instanceof \Closure) {
                var_dump($this->events['receive'] instanceof Closure, $this->events['receive']);
                dd('tcp服务必须要有回调事件: receive');
                exit;
            }

            if (empty($this->events['close']) || !$this->events['close'] instanceof \Closure) {
                dd('tcp服务必须要有回调事件: close');
                exit;
            }

            if (empty($this->events['request']) || !$this->events['request'] instanceof \Closure) {
                dd('tcp服务必须要有回调事件: request');
                exit;
            }
        } else if ($this->type == 'http') {
            
        }

    }

}