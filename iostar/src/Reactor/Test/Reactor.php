<?php
namespace IoStar\Reactor;

use \EventBase;
use \Event;

/**
 *
 */
class Reactor
{
    protected $reactor;

    protected $events;

    protected static $instance = null;

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

    public function add($fd, $what, $call)
    {
        $event = new Event($this->reactor, $fd, $what, $call);
        $event->add();
        $this->events[(int) $fd][$what] = $event;
        // switch ($what) {
        //   case self::READ:
        //     // code...
        //     break;
        //
        //   default:
        //     // code...
        //     break;
        // }
    }

    public function del($fd, $what)
    {
        ($this->events[(int) $fd][$what])->free();
    }

    public function run($value='')
    {
        $this->reactor->loop();
    }
}
