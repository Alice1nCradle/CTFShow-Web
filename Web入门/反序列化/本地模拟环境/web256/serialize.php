<?php
    class ctfShowUser
    {
        public $isVip = true;
        public $username ='114514';
        public $password ='xxxxxx';
    }
    $user = new ctfShowUser();
    echo(urlencode(serialize($user)));