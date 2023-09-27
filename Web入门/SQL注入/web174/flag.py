import base64

flag64 = "Y@CRmc@Bhvd@Cs@JYzcxYjRjYy@AmMjZkLTQ@BYjctYmU@JZS@AjYTMwMmFjZWU@EODZ@I"
flag = flag64.replace("@A", "1").replace("@B", "2").replace("@C", "3").replace("@D", "4").replace("@E", "5").replace(
    "@F", "6").replace("@G", "7").replace("@H", "8").replace("@I", "9").replace("@J", "0")

print(base64.b64decode(flag))
