<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
 * 首页
 *
 */
    public function index()
    {
        $this->success('请求成功');
    }
    /**
     * 测试
     *
     */
    public function demo()
    {
        $this->success('请求成功');
    }
    /**
     * 测试1
     *
     */
    public function demo1()
    {
        $this->success('请求成功');
    }
}
