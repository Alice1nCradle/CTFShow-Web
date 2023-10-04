## 爆破(AK)

### web21

启动靶机，进入环境，发现要登陆

先随便输入点什么进去，抓包，发现多了一行信息

```
Authorization: Basic MTIzNDU2OjEyMzQ1Ng==
```

将后面的编码进行解密后发现是123456:123456，这正是我刚刚输入进去的用户名:密码组合。

接下来对其进行爆破

payloadtype选Custom iterrator，position1填admin，position2填: ,position3导入字典

将字典导入后，记得base64编码

最后会得到一个状态为200的结果，重放它。

得到flag

![](F:/CTFShow-Web/Web入门/爆破/图片/web21.png)



### web22

爆破子域名，Maltego等工具随便用一个就是了



### web23

首先，他需要爆破

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-03 11:43:51
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-03 11:56:11
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/
error_reporting(0);

include('flag.php');
if(isset($_GET['token'])){
    $token = md5($_GET['token']);
    if(substr($token, 1,1)===substr($token, 14,1) && substr($token, 14,1) ===substr($token, 17,1)){
        if((intval(substr($token, 1,1))+intval(substr($token, 14,1))+substr($token, 17,1))/substr($token, 1,1)===intval(substr($token, 31,1))){
            echo $flag;
        }
    }
}else{
    highlight_file(__FILE__);

}
?>

```



照葫芦画瓢写个爆破脚本

```
<?php
    for ($i = 1; $i <= 5000; $i++){
        $token = md5($i);
        if (substr($token, 1, 1) === substr($token, 14, 1) && substr($token, 14, 1) === substr($token, 17, 1)) {
            if((intval(substr($token, 1,1)) + intval(substr($token, 14,1)) + substr($token, 17,1)) / substr($token, 1,1) === intval(substr($token, 31,1))){
                echo $i;
            }
            }
        }
?>
```



将其执行后显示的结果为422，这就是我们需要的值

payload:?token=422

获得flag

![](F:/CTFShow-Web/Web入门/爆破/图片/web23.png)

### web24

先看源代码：

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-03 13:26:39
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-03 13:53:31
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/

error_reporting(0);
include("flag.php");
if(isset($_GET['r'])){
    $r = $_GET['r'];
    mt_srand(372619038);
    if(intval($r)===intval(mt_rand())){
        echo $flag;
    }
}else{
    highlight_file(__FILE__);
    echo system('cat /proc/version');
}
```

代码中包含了flag.php，并接受了一个名为r的GET请求，接下来就是随机数的计算，如果随机数生成相等就给flag，没有就给你系统版本号。

编写爆破脚本

```
<?php
        echo mt_srand(372619038);
?>
```

payload:?r=1155388967



### web25

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-03 13:56:57
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-03 15:47:33
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


error_reporting(0);
include("flag.php");
if(isset($_GET['r'])){
    $r = $_GET['r'];
    mt_srand(hexdec(substr(md5($flag), 0,8)));
    $rand = intval($r)-intval(mt_rand());
    if((!$rand)){
        if($_COOKIE['token']==(mt_rand()+mt_rand())){
            echo $flag;
        }
    }else{
        echo $rand;
    }
}else{
    highlight_file(__FILE__);
    echo system('cat /proc/version');
}
```

需要一个rand，使它的值和随机值相等，若名为token的cookie等于后两次随机数之和。就给flag.

首先得找到rand，?r=0得rand=0-(-711790632)=711790632

需要使用php_mt_seed推断种子。

```
Pattern: EXACT
Version: 3.0.7 to 5.2.0
Found 0, trying 0xe8000000 - 0xebffffff, speed 43247.9 Mseeds/s
seed = 0xeb94492c = 3952363820 (PHP 3.0.7 to 5.2.0)
seed = 0xeb94492d = 3952363821 (PHP 3.0.7 to 5.2.0)
Found 2, trying 0xfc000000 - 0xffffffff, speed 46976.2 Mseeds/s
Version: 5.2.1+
Found 2, trying 0x60000000 - 0x61ffffff, speed 489.5 Mseeds/s
seed = 0x61c3a841 = 1640212545 (PHP 5.2.1 to 7.0.x; HHVM)
seed = 0x61c3a841 = 1640212545 (PHP 7.1.0+)
Found 4, trying 0xdc000000 - 0xddffffff, speed 483.7 Mseeds/s
seed = 0xdc10667b = 3692062331 (PHP 5.2.1 to 7.0.x; HHVM)
seed = 0xdc10667b = 3692062331 (PHP 7.1.0+)
Found 6, trying 0xfe000000 - 0xffffffff, speed 485.4 Mseeds/s
Found 6
./php_mt_seed 711790632  142.68s user 0.02s system 1594% cpu 8.947 total
```



写个脚本

```
<?php
    mt_srand(0xdc10667b);
    mt_rand();
    echo mt_rand()+mt_rand();
?>
```

得token=3138092968，上传得到flag

### web26

在打开页面后，并没有发现什么可疑的，但是使用burpsuite进行手抓包处理后， 发现访问的页面中有一个checkdb.php， 这个是在页面显示数据库连接成功后才有。

请求发送给服务器后，会是这样的

HTTP/1.1 200 OK Server: nginx/1.18.0 (Ubuntu) Date: Sun, 02 Apr 2023 02:00:37 GMT Content-Type: text/html; charset=UTF-8 Connection: close X-Powered-By: PHP/7.3.11 Content-Length: 122

尝试输入几个数据，以POST方式传输，由于缺少数据检验，空数据传输即可。



### web27

需要学号和密码，发现学生学籍信息查询系统，里面要姓名和身份证号码。

发现录取名单，对高先伊同学进行攻击。

BP爆破，密码明文存储，爆破生日。结果中有一个包长度不一样。

最后得到生日为19900201

查到学号，登录拿到flag



### web28

启动靶机，进入环境。找目录，全部爆破。

结果：/72/20/ 状态200

恭喜AK！