<?php

namespace addons\datatask\controller;

use think\addons\Controller;
use think\Db;
use app\admin\model\datatask\Config;
use app\admin\model\datatask\Backlog;
use addons\datatask\libs\Database;
use addons\datatask\logic\LogfileLogic;

class Index extends Controller
{

    public function index()
    {   
        $this->error("PAGE NOT FOUND");
    }

    public function backup()
    {
        if ($this->request->isCli()) {
            $code = $this->request->param("code", "");
            $model = Config::where(['name' => $code])->find();
            if (empty($model)) $this->fail("配置不存在");
            LogfileLogic::instance()->backup($model);
            $this->ok();
        }
    }

    /**
     * 备份最近的一份记录
     */
    public function restore()
    {
        if ($this->request->isCli()) {
            $code = $this->request->param('code', '');
            $model = Config::where(['name' => $code])->find();
            if (empty($model)) $this->fail("配置不存在");
            $backlogModel = Backlog::where('config_id', $model->id)->order('id DESC')->find();
            if (empty($backlogModel)) $this->fail("记录不存在");
            try {
                LogfileLogic::instance()->restore($backlogModel);
                $this->ok();
            } catch (\think\Exception $e) {
                $this->fail($e->getMessage());
            }
        }
    }

    public function down()
    {
        $code = $this->request->request("code", "");
        $token = $this->request->request('token', '');
        $model = Config::where(['name' => $code, 'token' => $token])->find();
        if (empty($model)) return $this->fail("配置不存在");
        $backlogModel = Backlog::where(['config_id' => $model->id])->order('id DESC')->find();
        if (empty($backlogModel)) $this->fail("记录不存在");
        if ($backlogModel->client_down_count >= $model->max_down) $this->fail("已超出下载次数");
        $backlogModel->client_down_count += 1;
        $backlogModel->save();
        LogfileLogic::instance()->down($backlogModel);
    }

    /**
     * 清理备份文件 只保留最近的一份
     */
    public function clear()
    {
        if ($this->request->isCli()) {
            $code = $this->request->param("code", "");
            $model = Config::where(['name' => $code])->find();
            if (empty($model)) return $this->fail("配置不存在");
            LogfileLogic::instance()->clear($model);
            $this->ok();
        }

    }

    private function fail($msg, $data = [], $code = 10000)
    {
        $this->write($msg, $data, $code);
    }

    private function ok($msg = '操作成功', $data = [], $code = 1)
    {
        $this->write($msg, $data, $code);
    }

    private function write($msg, $data = [], $code = 1)
    {
        echo json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

}
