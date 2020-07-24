<?php

namespace addons\datatask;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Datatask extends Addons
{
    public $name = 'datatask';
    public $menu = [
        [
            'name' => 'datatask',
            'title' => '数据表自动备份还原',
            'ismenu' => 1,
            'sublist' => [
                [
                    'name' => 'datatask/config',
                    'title' => '配置管理',
                    'ismenu' => 1,
                    'sublist' => [
                        ['name' => 'datatask/config/index', 'title' => '查看'],
                        ['name' => 'datatask/config/addd', 'title' => '添加'],
                        ['name' => 'datatask/config/edit', 'title' => '编辑'],
                        ['name' => 'datatask/config/del', 'title' => '删除'],
                    ]
                ],
                [
                    'name' => 'datatask/backlog',
                    'title' => '备份记录',
                    'ismenu'=> 1,
                    'sublist' => [
                        ['name' => 'datatask/backlog/index', 'title' => '查看'],
                        ['name' => 'datatask/backlog/down', 'title' => '下载'],
                        ['name' => 'datatask/backlog/restorelog', 'title' => '还原'],
                    ]
                ],
                [
                    'name' => 'datatask/logs',
                    'title' => '日志',
                    'ismenu' => 1,
                    'sublist' => [
                        ['name' => 'datatask/logs/index', 'title' => '查看'],
                        ['name' => 'datatask/logs/del', 'title' => '删除']
                    ]
                ]
            ]
        ]
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        Menu::create($this->menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete($this->name);
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable($this->name);
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable($this->name);
        return true;
    }

}
