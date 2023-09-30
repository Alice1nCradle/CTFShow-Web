## 信息搜集（AK）

### web1

启动靶机，进入环境，提示“开发注释没有删除干净”，尝试打开F12，发现了flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web1.png)



### web2

提示：js前台拦截 === 无效操作

启动靶机，进入环境，显示无法查看源代码，按F12确实没用。

但是，有一个view-source:协议是浏览器无法过滤的，利用它查看到源代码。

并且得到flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web2.png)



### web3

提示：没思路的时候抓个包看看，可能会有意外收获

此处需要抓包软件，目前推荐的**BurpSuite**，有能力搞个专业版最好，社区版锁了算力。

启动靶机，进入环境，抓个包先，先不管发出去的。

等靶机响应时BurpSuite有了反应，查看具体内容，发现flag就藏在响应中。解决了

![](F:/CTFShow-Web/Web入门/信息收集/图片/web3.png)



### web4

总有人把后台地址写入robots，帮黑阔大佬们引路。

看来需要后台了，先使用dirsearch扫一遍目录，结果如下

![](F:/CTFShow-Web/Web入门/信息收集/图片/web4/扫目录.png)

有一个robots.txt可供他人访问，这也与提示相符。

尝试访问得到内容：

```
User-agent: *
Disallow: /flagishere.txt
```

说明不限制浏览器，但不允许爬取flagishere.txt，那我自己访问总没事了吧。你问我怎么知道这个东西的？你自己告诉我的。

![](F:/CTFShow-Web/Web入门/信息收集/图片/web4/拿下.png)



### web5

phps源码泄露有时候能帮上忙

启动靶机，进入环境，先扫一下phps看有什么东西没有

还真找到了

![](F:/CTFShow-Web/Web入门/信息收集/图片/web5/找到你了.png)

然后访问它，发现文件被下载下来了。

打开就能够得到flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web5/找到flag.png)



### web6

解压源码到当前目录，测试正常，收工

本题考查源码泄露

那还用说吗？扫一下压缩包，看有没有尚未被删除的备份。

果然找到了，一般都以www.zip或者WWW.zip标明

![](F:/CTFShow-Web/Web入门/信息收集/图片/web6/泄露源码.png)

访问它，将它下载下来。

里面有一个index.php，注释写明flag在另一个文件里面，这就得到了flag，记得弄到网上访问它！

![](F:/CTFShow-Web/Web入门/信息收集/图片/web6/最终结果.png)



### web7

版本控制很重要，但不要部署到生产环境更重要。

这里主要指的是**版本控制软件**git和svn，说明一会儿又要扫目录了。

很好，刚开始扫描就发现.git的重定向了，即访问.git会自动重定向到它的存档里面。

访问即可得到flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web7.png)



### web8

版本控制很重要，但不要部署到生产环境更重要。

同web7，扫一下发现了.svn

访问得到flag



![](F:/CTFShow-Web/Web入门/信息收集/图片/web8.png)



### web9

发现网页有个错别字？赶紧在生产环境vim改下，不好，死机了

> vim在进行文件读写时第一次会生成.swp文件，因意外退出后会产生.swo文件，再出现意外会生成.swn文件，而这些文件在意外退出等情况下不会被删除。因此可以用它们得到信息。

访问index.php.swp，发现文件下载下来了，打开发现flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web9.png)



### web10

cookie 只是一块饼干，不能存放任何隐私数据

那就把cookie弄到手，BurpSuite,启动！

flag就在set-cookie值中。

![](F:/CTFShow-Web/Web入门/信息收集/图片/web10.png)



### web11

域名其实也可以隐藏信息，比如flag.ctfshow.com 就隐藏了一条信息

域名信息需要域名解析服务器告诉我们，所以我们需要问DNS。

同时，题目中写了是txt记录，也就是我们需要确定查询范围

在cmd中输入nslookup -type=txt flag.ctfshow.com即可得到flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web11.png)



### web12

有时候网上的公开信息，就是管理员的常用密码

进入靶机，是一个网站，先扫目录

![](F:/CTFShow-Web/Web入门/信息收集/图片/web12/目录扫描结果.png)

看来可利用的部分为admin（401状态，说明服务器需要认证身份）和robots.txt

先访问robots.txt，这里信息

```
User-agent: *
Disallow: /admin/
```

也表示admin目录是一个非常重要的东西。

直接访问发现不知道密码和用户名，根据提示，用户名猜admin，密码猜网站最底下的联系电话。

通过了，拿到flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web12/得到flag.png)



### web13

技术文档里面不要出现敏感信息，部署到生产环境后及时修改默认密码

社会工程学快乐题

扫个目录发现几乎全部都要认证，得找一下口令，那想都不要想，找文档

然后网站底部有个document，把它下载下来后发现最后一页附上了登陆后台和账号密码。

登陆就拿到flag了。

![](F:/CTFShow-Web/Web入门/信息收集/图片/web13.png)



### web14

有时候源码里面就能不经意间泄露重要(editor)的信息,默认配置害死人

启动靶机，进入环境。输入/editor查看编辑器。这个编辑器上传文件时直接访问了服务器自身的目录。

找吧找吧。在var/www/html目录 中，存在 一个nothinghere有一个文件 fl000g.txt 直接访问这个fl000g.txt 即可获得flag!

![](F:/CTFShow-Web/Web入门/信息收集/图片/web14.png)







### web15

公开的信息比如邮箱，可能造成信息泄露，产生严重后果

又是社会工程学问题

先扫描目录，发现有个/admin

访问/admin页面 发现有一个忘记密码操作，需要输入地址 在主页面下面看到QQ邮箱，通过QQ号查询邮箱，是西安的 修改密码成功，用户名 admin 登录成功获得flag



### web16

对于测试用的探针，使用完毕后要及时删除，可能会造成信息泄露

启动靶机，进入环境，在url后面加上/tz.php，进入探针，查看phpinfo，在里面找到flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web16.png)



### web17

备份的sql文件会泄露敏感信息

那就寻找sql文件的备份，扫描目录发现有个backup.sql

下载下来打开即可找到flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web17.png)



### web18

不要着急，休息，休息一会儿，玩101分给你flag

你赢了，去幺幺零点皮爱吃皮看看

然后得到了flag

![](F:/CTFShow-Web/Web入门/信息收集/图片/web18.png)



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

![](F:/CTFShow-Web/Web入门/信息收集/图片/web19.png)



### web20

mdb文件是早期asp+access构架的数据库文件，文件泄露相当于数据库被脱裤了。

这个文件放在/db/db.mdb里面

下载下来打开即可得到flag

