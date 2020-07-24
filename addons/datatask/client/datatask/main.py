from apscheduler.schedulers.blocking import BlockingScheduler
from apscheduler.triggers.cron import CronTrigger
from datetime import datetime
import worker
import configparser
import os
import sys
import const

def run():
    cfgpath = os.path.join(const.ROOT_PATH, './config.ini')
    conf = configparser.ConfigParser()
    conf.read(cfgpath, encoding="utf-8")

    scheduler = BlockingScheduler()

    tasks = conf.get('main', 'task').split(',')
    for code in tasks:
        job = 'job_' + code
        token = conf.get(code, 'token')
        url = conf.get('main', 'url')
        cron = conf.get(code, 'cron').split(' ')
        def job(code, token, url):
            worker.run(code = code, token = token, url = url)
        trigger = CronTrigger(second = cron[0], minute = cron[1], hour = cron[2], day = cron[3], month = cron[4], year = cron[5])
        scheduler.add_job(job, trigger, args = [code, token, url])
    print("共" + str(len(tasks)) + "个任务执行中：" + conf.get('main', 'task') + "")
    scheduler.start()


