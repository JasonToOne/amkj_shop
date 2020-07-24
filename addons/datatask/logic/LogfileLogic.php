<?php

namespace addons\datatask\logic;

use think\Exception;
use think\Db;
use addons\datatask\libs\Database;

class LogfileLogic
{

    const LOG_TYPE_BACKUP = 0; // 备份
    const LOG_TYPE_RESTORE = 1;// 还原
    const LOG_TYPE_DOWN = 2;   // 下载
    const LOG_TYPE_CLEAR = 3;   // 清理

    const CLIENT_TYPE_CLI = 0;      // CLI 通过cli访问
    const CLIENT_TYPE_URL = 1;    // URL 通过请求url访问

    public static function instance()
    {
        return new self;
    }

    public function __construct()
    {
        $this->option = get_addon_config('datatask');
        $this->database = Database::instance($this->option);
    }

    public function backup($configModel) 
    {
        $file = $this->database->backup($configModel['tables']);
        $data = [
            'config_id' => $configModel['id'],
            'path' => $file,
            'create_time' => time()
        ];
        Db::name('datatask_backlog')->insert($data);
        $configModel->back_count += 1;
        $configModel->save();
        $this->addLog(self::LOG_TYPE_BACKUP);
    }

    public function down($backlogModel)
    {
        $filepath = $backlogModel->path;
        $file = fopen($filepath, 'rb');
        $file_dir = './down/';
        Header("Content-type: application/octet-stream" ); 
        Header("Accept-Ranges: bytes" );  
        Header("Accept-Length: " . filesize($filepath));  
        Header("Content-Disposition: attachment; filename=" . explode(DS, $filepath)[count(explode(DS, $filepath)) - 1]); 
        echo fread($file, filesize($filepath));    
        fclose($file);
        Db::name('datatask_config')->where('id', $backlogModel->config_id)->setInc('down_count', 1);
        $this->addLog(self::LOG_TYPE_DOWN);
        exit;
    }

    /**
     * 还原数据表
     */
    public function restore($backlogModel)
    {
        try {
            $filepath = $backlogModel->path;
            $this->database->restore($filepath);
            $this->addLog(self::LOG_TPYE_RESTORE);
        } catch(Exception $e) {
            throw $e;
        }
    }

    /**
     * 清空备份的文件，只保留最近的一份
     */
    public function clear($configModel)
    {
        try {
            $ids = $this->getClearQuery($configModel->id)->column('id');
            $files = $this->getClearQuery($configModel->id)->column('path');
            if (!empty($ids)) {
                $files = array_splice($files, 1);
                $ids = array_splice($ids, 1);
                foreach ($files as $file) {
                    unlink($file);
                }
                Db::name('datatask_backlog')->where('id', 'IN', $ids)->delete();
                $this->addLog(self::LOG_TYPE_CLEAR);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function addLog($type = 0, $data = '') 
    {
        $user = \app\admin\library\Auth::instance()->getUserInfo();
        $request = request();
        $data = [
            'type' => $type,
            'client_type' => $request->isCli() ? self::CLIENT_TYPE_CLI : self::CLIENT_TYPE_URL,
            'data' => is_array($data) ? json_encode($data) : $data,
            'url' => $request->url(),
            'ip' => $request->ip(),
            'user' => empty($user) ? '' : collection([
                'id' => $user['id'],
                'nickname' => $user['nickname'],
                'username' => $user['username']
            ]),
            'useragent' => $request->header('User-Agent'),
            'create_time' => time()
        ];
        Db::name('datatask_logs')->insert($data);
    }
    
    private function getClearQuery($id)
    {
        return Db::name('datatask_backlog')->where(['config_id' => $id])->order('id DESC');
    }

}