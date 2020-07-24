<?php

namespace addons\datatask\libs;

use think\Db;
use think\Exception;

class Database
{
    public $config = [
        'package_size' => 10,
        'backup_path' => RUNTIME_PATH . 'backup' . DS,
        'tmp_path' => RUNTIME_PATH . 'backup' . DS . 'tmp' . DS
    ];

    private $file_index = 1;

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->tmp_path = $this->config['tmp_path'] . 'backup-' . microtime() . DS;
    }

    public static function instance($config = []) {
        return new self($config);
    }

    public function buildCreateSql($table)
    {
        return "DROP TABLE IF EXISTS `$table`;" . PHP_EOL . Db::query("SHOW CREATE TABLE " . $table)[0]['Create Table'] . ';--END--' . PHP_EOL;
    }


    /**
     * 备份数据表
     */
    public function backup($table_list)
    {
        $table_list = $this->getTables($table_list);
        if (empty($table_list)) throw new Exception("数据表不存在");
        $content = "";
        foreach ($table_list as $table) {
            try {
                $content .= $this->buildCreateSql($table);
                if (strlen($content) >= $this->config['package_size'] * 1024 * 1024) {
                    $this->write($content);
                    $content = "";
                }
            } catch (\PDOException $e) {}
        }
        foreach ($table_list as $table) {
            try {
                $res = Db::query("SELECT * FROM ". $table );
                if (!count($res)) continue;
                foreach ($res as $row) {
                    $sql = "INSERT INTO " . $table . " VALUES(";
                    $data = [];
                    foreach ($row as $vo) {
                        $v = str_replace(["\\", "'"], ["\\\\", "\\'"], $vo);
                        $data[] = is_null($vo) ? 'NULL' : (is_numeric($vo) ? $v : "'" . $v . "'");
                    }
                    $sql .= implode(',', $data). ");--END--" . PHP_EOL;
                
                    $content .= $sql;
                    if (strlen($content) >= $this->config['package_size'] * 1024 * 1024) {
                        $this->write($content);
                        $content = "";
                    }
                }
            } catch (\PDOException $e) {}
        }
        $this->write($content);
        $this->file_index = 1;
        return $this->zip();
    }

    /**
     * 还原数据表
     */
    public function restore($zip_file)
    {
        $this->tmp_path = $this->config['tmp_path'] . '_backup' . '-' . microtime() . DS;
        $zip = new \ZipArchive;
        if (true === $zip->open($zip_file)) {
            $zip->extractTo($this->tmp_path);
            $zip->close();
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tmp_path), \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $this->exec($file->getRealPath());
            }
        }

        @rmdirs($this->tmp_path);

    }

    /**
     * 执行sql语句
     */
    protected function exec($filename)
    {
        $lines = file($filename);
        $templine = "";
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*')
                continue;
            $templine .= $line;
            if (substr(trim($line), -7, 7) == '--END--') {
                $templine = str_ireplace('__PREFIX__', config('database.prefix'), $templine);
                $templine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $templine);
                try {
                    Db::getPdo()->exec($templine);
                } catch (\PDOException $e) {
                    throw new Exception($e->getMessage());
                }
                $templine = '';
            }
        }
    }

    protected function write($content)
    {
        if (!is_dir($this->tmp_path)) {
            @mkdir($this->tmp_path, 0755, true);
        }
        file_put_contents($this->tmp_path . time() . '-' . $this->file_index . '.sql', $content);
        $this->file_index ++;
    }

    protected function zip()
    {
        $zip = new \ZipArchive;
        $zip_filename = $this->config['backup_path'] . 'backup-' . time() . '.' . \fast\Random::numeric(8) . '.zip';
        $zip->open($zip_filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tmp_path), \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = str_replace(DS, '/', substr($filePath, strlen($this->tmp_path)));
                if (!in_array($file->getFilename(), ['.git', '.DS_Store', 'Thumbs.db'])) {
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
        $zip->close();
        @rmdirs($this->tmp_path);
        return $zip_filename;
    }

    protected function getTables($str)
    {
        if (strpos('%', $str) != -1) {
            $database = \think\Config::get('database.database');
            $prefix = \think\Config::get('database.prefix');
            $sql = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema='$database' AND TABLE_NAME LIKE ?";
            $kw = str_replace(["__PREFIX__", "_"], [$prefix, "\\_"], trim($str));
            $rs = Db::query($sql, [$kw]);
            $result = [];
            if (!empty($rs)) {
                foreach ($rs as $item) {
                    $result[] = $item['TABLE_NAME'];
                }
            }
            return $result;
        }
        return is_string($str) ? explode(',', $str) : $str;
    }

    
}