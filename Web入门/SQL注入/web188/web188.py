import requests

url = 'http://106c7788-8a93-4a71-ab55-e09f7ecddca2.challenge.ctf.show/api/'

payload = {
    "username":"0",
    "password":"0"
}

res = requests.post(url=url,data=payload).text

print(res)
