<?php

namespace app\admin\controller\datatask;

use think\Db;
use think\Config;

class Index extends \app\common\controller\Backend
{
    /**
     * 获取数据表
     */
    public function get_tables()
    {
        $database = Config::get('database.database');
        $keyword = $this->request->request('search', '');
        $keyword = empty($keyword) ? '' : str_replace('_', '\\_', $keyword);
        $sql = "SELECT * FROM information_schema.tables WHERE table_schema='$database' AND TABLE_NAME LIKE CONCAT('%',?,'%')";
        $rs = Db::query($sql, [$keyword]);
        return json(['total' => count($rs), 'rows' => $rs]);
    }

    /**
     * 刷新token
     */
    public function refresh_token()
    {
        return $this->success('Token已刷新', '', \fast\Random::uuid());
    }
}