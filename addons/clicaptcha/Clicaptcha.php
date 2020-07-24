<?php

namespace addons\clicaptcha;

use think\Addons;
use think\Validate;

/**
 * 全新点选验证码插件
 */
class Clicaptcha extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {

        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {

        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {

        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {

        return true;
    }

    /**
     * 自定义captcha验证事件
     */
    public function actionBegin()
    {
        Validate::extend('captcha', function ($value, $id = "") {
            $clicaptcha = new \addons\clicaptcha\library\Clicaptcha();
            if (!$clicaptcha->check(request()->post("captcha"), true)) {
                return false;
            };
            return true;
        });
    }

}
