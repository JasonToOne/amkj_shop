<?php

namespace app\admin\controller\datatask;

use app\common\controller\Backend;

use addons\datatask\libs\Database;
use addons\datatask\logic\LogfileLogic;
use think\Exception;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Config extends Backend
{
    
    /**
     * Config模型对象
     * @var \app\admin\model\datatask\Config
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\datatask\Config;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 添加
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            $this->view->assign('token', \fast\Random::uuid());
        }
        return parent::add();
    }

    /**
     * 执行
     */
    public function run()
    {
        $model = $this->model->get($this->request->get('id'));
        if ($model) {
            try {
                LogfileLogic::instance()->backup($model);
                $this->success();
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $this->error("记录不存在");
    }

    /**
     * 清理备份文件
     */
    public function clear()
    {
        $model = $this->model->get($this->request->get('id'));
        if ($model) {
            try {
                LogfileLogic::instance()->clear($model);
                $this->success();
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $this->error("记录不存在");
    }
}
