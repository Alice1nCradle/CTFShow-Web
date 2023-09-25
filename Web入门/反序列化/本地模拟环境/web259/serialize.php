<?php
//token=ctfshow
$client = new SoapClient(null,array('uri'=>'127.0.0.1','location'=>'http://127.0.0.1:7799'));//创建一个对象，里面的参数第一个一般为null，第二个参数是数组形式，想要了解更多自行百度。
$client->getFlag();//这里我们调用一个不存在的方法来触发SoapClient类的__call函数，据我了解该默认__call函数会发送此请求给服务器。
?>