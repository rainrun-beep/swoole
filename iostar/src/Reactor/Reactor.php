<?php
namespace IoStar\Reactor;

use \Event as Event;
use \EventBase as EventBase;


/**
 * 设置绑定事件类型
 * reactor的类
 * Reactor模式首先是事件驱动的，有一个或多个并发输入源，有一个Service Handler，有多个Request Handlers；这个Service Handler会同步的将输入的请求（Event）多路复用的分发给相应的Request Handler.
 */
class Reactor
{
    protected $reactor;

    protected $events;

    public static $instance = null;

    const READ = Event::READ | Event::PERSIST;

    const WRITE = Event::WRITE | Event::PERSIST;

    const EVENT = Event::READ | Event::WRITE | Event::PERSIST;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$instance->reactor = new EventBase;
        }
        return self::$instance;
    }

    public function add($fd, $what, $cb, $arg = null)
    {

        switch ($what) {
            case self::READ:
                $event = new Event($this->reactor, $fd, self::READ, $cb, $arg);
                break;
            case self::WRITE:
                $event = new Event($this->reactor, $fd, self::WRITE, $cb, $arg);
                break;
            default:
                $event = new Event($this->reactor, $fd, $what, $cb, $arg);
                break;
        }

        $event->add();
        $this->events[(int) $fd][$what] = $event;
    }

    public function del($fd, $what = 'all')
    {
        dd($fd, "清楚");
        $events = $this->events[(int) $fd];
        $events[$what]->free();
        dd($fd, "清楚ok");
        
        // if ($what == 'all') {
        //     foreach ($events as $event) {
        //         $event->free();
        //     }
        // } else {
        //     if ($what != self::READ && $what != self::WRITE) {
        //         throw new \Exception('不存在的事件');
        //     }
        //     $events[$what]->free();
        // }
    }

    public function run()
    {
        $this->reactor->loop();
    }
}
?>
