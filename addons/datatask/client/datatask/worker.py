import configparser
import os
import sys
import requests
import json
import time
import const

def run(code, token, url):
    r = requests.get(url, params = {"code": code, "token": token})
    if (r.status_code != 200):
        print('status_code:' + str(r.status_code))
        return
    if (r.headers['Content-Type'] == "application/octet-stream"):
        curpath = os.path.join(const.ROOT_PATH, './down/', time.strftime("%Y-%m-%d", time.localtime()) + '/' + code + '/')
        if (not os.path.isdir(curpath)) :
            os.makedirs(curpath)
        filename = os.path.join(curpath, r.headers['Content-Disposition'].split('=')[1])
        with open(file = filename, mode = 'wb') as f:
            for chunk in r.iter_content(chunk_size = 512):
                f.write(chunk)
        msg = "[" + code + "] 文件已下载:" + os.path.abspath(filename)
        print(msg)
    else :
        res = r.json()
        print(res['msg'])