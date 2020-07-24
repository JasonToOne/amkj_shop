from datetime import datetime
import worker
import configparser
import os
import sys
import const

cfgpath = os.path.join(const.ROOT_PATH, './config.ini')
conf = configparser.ConfigParser()
conf.read(cfgpath, encoding="utf-8")


tasks = conf.get('main', 'task').split(',')
for code in tasks:
    token = conf.get(code, 'token')
    url = conf.get('main', 'url')
    worker.run(code = code, token = token, url = url)
os.system('pause')

