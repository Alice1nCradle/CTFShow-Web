<?php
	class ctfShowUser
	{
    	public $isVip=true;
	}
	$a = new ctfShowUser();
	echo(urlencode(serialize($a)));
	