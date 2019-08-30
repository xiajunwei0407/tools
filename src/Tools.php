<?php

namespace Xjw;

/**
 * 助手类
 * Class Tools
 * @package Xjw
 */
class Tools
{
    /**
     * 记录日志，默认在项目根目录创建日期目录 /xjw-log/Y/m/d.log
     * @param $data 数据
     * @param string $fileName 完整文件名
     */
    public static function log($data, $fileName = '')
    {
        if (!$fileName){
            $fileName = $_SERVER['DOCUMENT_ROOT'] .
                DIRECTORY_SEPARATOR . 'xjw-log' .
                DIRECTORY_SEPARATOR . date('Y') .
                DIRECTORY_SEPARATOR . date('m') .
                DIRECTORY_SEPARATOR . date('d') . '.log';
        }
        if(!is_dir(dirname($fileName))){
            @mkdir(dirname($fileName), 0777, true);
        }
        date_default_timezone_set('Asia/Shanghai');
        if(is_array($data) || is_object($data)){
            $logData = '[ ' . date('Y-m-d H:i:s') . ' ]' . PHP_EOL . PHP_EOL
                . var_export($data, true) . PHP_EOL . PHP_EOL
                . '+------------------------------------------------------------------------+' . PHP_EOL . PHP_EOL;
        }else{
            $logData = '[ ' . date('Y-m-d H:i:s') . ' ]' . PHP_EOL . PHP_EOL
                . $data . PHP_EOL . PHP_EOL
                . '+------------------------------------------------------------------------+' . PHP_EOL . PHP_EOL;
        }
        @file_put_contents($fileName, $logData, FILE_APPEND);
    }

    /**
     * 变量友好化打印输出
     * @param variable  $param  可变参数
     * @example dump($a,$b,$c,$e,[.1]) 支持多变量，使用英文逗号符号分隔，默认方式 print_r，查看数据类型传入 .1
     * @version php>=5.6
     * @return void
     */
    public static function dump(...$param)
    {
        echo '<style>.php-print{background:#eee;padding:10px;border-radius:4px;border:1px solid #ccc;line-height:1.5;white-space:pre-wrap;font-family:Menlo,Monaco,Consolas,"Courier New",monospace;font-size:13px;}</style>', '<pre class="php-print">';
        if (end($param) === .1) {
            array_splice($param, -1, 1);
            foreach ($param as $k => $v) {
                echo $k > 0 ? '<hr>' : '';
                ob_start();
                var_dump($v);
                echo preg_replace('/]=>\s+/', '] => <label>', ob_get_clean());
            }
        } else {
            foreach ($param as $k => $v) {
                echo $k > 0 ? '<hr>' : '', print_r($v, true); // echo 逗号速度快
            }
        }
        echo '</pre>';
        die;
    }

}