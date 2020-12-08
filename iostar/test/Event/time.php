<?php
// 设置事件的应用类
$eventBase = new EventBase();

// 定义事件类
$event = new Event($eventBase, -1, Event::TIMEOUT | Event::PERSIST, function(){
  // 事件类的动作
  echo microtime(true). ": 开始了\n";
});
$event->add(2);
// 定义事件类
$event1 = new Event($eventBase, -1, Event::TIMEOUT | Event::PERSIST, function(){
  // 事件类的动作
  echo microtime(true). ": 检查人员\n";
});
$event1->add(0.5);

// 启动
$eventBase->loop();
