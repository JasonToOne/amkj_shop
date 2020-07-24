<?php

namespace app\admin\controller\datatask;

use app\common\controller\Backend;

use addons\datatask\logic\LogfileLogic;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Backlog extends Backend
{
    
    /**
     * Backlog模型对象
     * @var \app\admin\model\datatask\Backlog
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\datatask\Backlog;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        $config_id = $this->request->get('config_id');
        
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $where1 = function($q) use ($config_id) {
                if (!empty($config_id)) {
                    $q->where('config_id', $config_id);
                }
            };
            $total = $this->model
                    ->with(['datataskconfig'])
                    ->where($where)
                    ->where($where1)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['datataskconfig'])
                    ->where($where)
                    ->where($where1)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                
                
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 下载
     */
    public function down()
    {
        $model = $this->model->find($this->request->request('id'));
        if ($model) {
            LogfileLogic::instance()->down($model);
        }
        else $this->error("记录不存在");
    }

    /**
     * 还原
     */
    public function restorelog()
    {
        $model = $this->model->find($this->request->request('id'));
        if ($model) {
            try {
                LogfileLogic::instance()->restore($model);
                $this->success();
            } catch (\think\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        else $this->error("记录不存在");
    }
}
