<?php
namespace IoStar\Async;

use \Event;

class Events
{
   protected $client;

   protected $eventBase;

   function __construct($eventBase, $client)
   {
       $this->eventBase = $eventBase;
       $this->client = $client;
   }

   public function handler(Worker $server, &$count)
   {
       $event = new Event($this->eventBase, $this->client,  Event::PERSIST |Event::READ | Event::WRITE , function($socket) use ($server, &$count){

           $server->sendMessage($socket);
           // 对于建立处理时间
           ($count[(int) $socket][Event::PERSIST | Event::READ | Event::WRITE])->free();
       });
       $event->add();
       $count[(int) $this->client][Event::PERSIST | Event::READ | Event::WRITE] = $event;
   }
}
