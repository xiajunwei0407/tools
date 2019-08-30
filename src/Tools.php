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
    static function log($data, $fileName = '')
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
    static function dump(...$param)
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
//                echo $k > 0 ? '<hr>' : '', print_r($v, true); // echo 逗号速度快
                echo $k > 0 ? '<hr>' : '', is_array($v) || is_object($v) || is_bool($v) ? var_export($v, true) : print_r($v, true); // echo 逗号速度快
            }
        }
        echo '</pre>';
        die;
    }

    /**
     *  获取客户端ip
     *
     * @param int $type
     * @return mixed
     */
    static function getClientIp($type = 0)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL) return $ip[$type];
        if (@$_SERVER['HTTP_X_REAL_IP']) {//nginx 代理模式下，获取客户端真实IP
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }


    /**
     * 随机生成编码.
     *
     * @author
     *
     * @param $len 长度.
     * @param int $type 1:数字 2:字母 3:混淆
     * @return string
     */
    static function getRandCode($len, $type = 1)
    {
        $output = '';
        $str = ['a', 'b', 'c', 'd', 'e', 'f', 'g',
            'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
            's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];
        $num = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        switch ($type) {
            case 1:
                $chars = $num;
                break;
            case 2:
                $chars = $str;
                break;
            default:
                $chars = array_merge($str, $num);
        }

        $chars_len = count($chars) - 1;
        shuffle($chars);

        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $chars_len)];
        }

        return $output;
    }


    /**
     * 数组转换成xml.
     *
     * @author yzm
     *
     * @param $arr 数组
     *
     * @return string xml结果
     */
    static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";

            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }


    /**
     * 将xml转为数组.
     *
     * @param $xml xml数据
     *
     * @return array|mixed|stdClass
     */
    static function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     * 获取汉字首字母
     * @param $s0 输入的汉字，推荐一个字，多个可能不准确
     * @return string|null
     */
    static function getChineseFirstCode($s0)
    {
        $fchar = ord($s0{0});
        if ($fchar >= ord("A") and $fchar <= ord("z")) return strtoupper($s0{0});
        $s1 = iconv("UTF-8", "gb2312", $s0);
        $s2 = iconv("gb2312", "UTF-8", $s1);
        if ($s2 == $s0) {
            $s = $s1;
        } else {
            $s = $s0;
        }
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 and $asc <= -20284) return "A";
        if ($asc >= -20283 and $asc <= -19776) return "B";
        if ($asc >= -19775 and $asc <= -19219) return "C";
        if ($asc >= -19218 and $asc <= -18711) return "D";
        if ($asc >= -18710 and $asc <= -18527) return "E";
        if ($asc >= -18526 and $asc <= -18240) return "F";
        if ($asc >= -18239 and $asc <= -17923) return "G";
        if ($asc >= -17922 and $asc <= -17418) return "I";
        if ($asc >= -17417 and $asc <= -16475) return "J";
        if ($asc >= -16474 and $asc <= -16213) return "K";
        if ($asc >= -16212 and $asc <= -15641) return "L";
        if ($asc >= -15640 and $asc <= -15166) return "M";
        if ($asc >= -15165 and $asc <= -14923) return "N";
        if ($asc >= -14922 and $asc <= -14915) return "O";
        if ($asc >= -14914 and $asc <= -14631) return "P";
        if ($asc >= -14630 and $asc <= -14150) return "Q";
        if ($asc >= -14149 and $asc <= -14091) return "R";
        if ($asc >= -14090 and $asc <= -13319) return "S";
        if ($asc >= -13318 and $asc <= -12839) return "T";
        if ($asc >= -12838 and $asc <= -12557) return "W";
        if ($asc >= -12556 and $asc <= -11848) return "X";
        if ($asc >= -11847 and $asc <= -11056) return "Y";
        if ($asc >= -11055 and $asc <= -10247) return "Z";

        return null;
    }


    /**
     * 是否是微信,如果是则返回微信版本.
     *
     * @author yzm
     *
     * @return bool
     */
    static function isWeiXin()
    {
        $rst = false;
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (strpos($user_agent, 'MicroMessenger') !== false) {
            // 获取版本号
            preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
            $rst = @$matches[2];
        }
        return $rst;
    }


    /**
     * 友好的时间显示
     *
     * @author yzm
     *
     * @param int $sTime 待显示的时间
     * @param string $type 类型. normal | mohu | full | ymd | other
     * @param string $alt 已失效
     *
     * @return string
     */
    static function friendlyDate($sTime, $type = 'normal', $alt = 'false')
    {
        if (!$sTime) return '';
        //sTime=源时间，cTime=当前时间，dTime=时间差

        $cTime = time();
        $dTime = $cTime - $sTime;
        $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));
        $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));

        //normal：n秒前，n分钟前，n小时前，日期
        switch ($type) {
            case 'normal':
                if ($dTime < 60) {
                    if ($dTime < 10) {
                        return '刚刚';
                    } else {
                        return intval(floor($dTime / 10) * 10) . "秒前";
                    }
                } elseif ($dTime < 3600) {
                    return intval($dTime / 60) . "分钟前";
                    //今天的数据.年份相同.日期相同.
                } elseif ($dYear == 0 && $dDay == 0) {
                    //return intval($dTime/3600)."小时前";
                    return '今天' . date('H:i', $sTime);
                } elseif ($dYear == 0) {
                    return date("m月d日 H:i", $sTime);
                } else {
                    return date("Y-m-d H:i", $sTime);
                }
                break;
            case 'mohu':
                if ($dTime < 60) {
                    return $dTime . "秒前";
                } elseif ($dTime < 3600) {
                    return intval($dTime / 60) . "分钟前";
                } elseif ($dTime >= 3600 && $dDay == 0) {
                    return intval($dTime / 3600) . "小时前";
                } elseif ($dDay > 0 && $dDay <= 7) {
                    return intval($dDay) . "天前";
                } elseif ($dDay > 7 && $dDay <= 30) {
                    return intval($dDay / 7) . '周前';
                } elseif ($dDay > 30) {
                    return intval($dDay / 30) . '个月前';
                }
                break;
            case 'full':
                return date("Y-m-d , H:i:s", $sTime);
                break;
            case 'ymd':
                return date("Y-m-d", $sTime);
                break;
            default:
                if ($dTime < 60) {
                    return $dTime . "秒前";
                } elseif ($dTime < 3600) {
                    return intval($dTime / 60) . "分钟前";
                } elseif ($dTime >= 3600 && $dDay == 0) {
                    return intval($dTime / 3600) . "小时前";
                } elseif ($dYear == 0) {
                    return date("Y-m-d H:i:s", $sTime);
                } else {
                    return date("Y-m-d H:i:s", $sTime);
                }
                break;
        }
    }

    /**
     * 时间差值
     * @param $begin_time
     * @param $end_time
     * @return array
     */
    static function getTimeDiff($begin_time, $end_time){
        if ($begin_time < $end_time) {
            $starttime = $begin_time;
            $endtime   = $end_time;
        } else {
            $starttime = $end_time;
            $endtime   = $begin_time;
        }
        $timediff = $endtime - $starttime;
        $days     = intval($timediff / 86400);
        $remain   = $timediff % 86400;
        $hours    = intval($remain / 3600);
        $remain   = $remain % 3600;
        $mins     = intval($remain / 60);
        $secs     = $remain % 60;
        $str = $days . '天' . $hours . '小时' . $mins . '分' . $secs . '秒';
        return [
            'days'  => $days,
            'hours' => $hours,
            'mins'  => $mins,
            'secs'  => $secs,
            'str'   => $str
        ];
    }

    /**
     * 执行shell脚本.
     *
     * @author yzm
     *
     * @param $cmd
     * @return string
     */
    static function execShell($cmd)
    {
        $res = '';
        if (function_exists('system')) {
            ob_start();
            system($cmd);
            $res = ob_get_contents();
            ob_end_clean();
        } elseif (function_exists('shell_exec')) {
            $res = shell_exec($cmd);
        } elseif (function_exists('exec')) {
            exec($cmd, $res);
            $res = join("\n", $res);
        } elseif (function_exists('passthru')) {
            ob_start();
            passthru($cmd);
            $res = ob_get_contents();
            ob_end_clean();
        } elseif (is_resource($f = @popen($cmd, "r"))) {
            $res = '';
            while (!feof($f)) {
                $res .= fread($f, 1024);
            }
            pclose($f);
        }

        return $res;
    }

    /**
     * 得到微妙.
     *
     * @return float
     *
     * @author yzm
     */
    static function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * 函数的含义说明：CURL发送post请求    获取数据
     *
     * @access public
     * @param str          $url     发送接口地址
     * @param array/json   $data    要发送的数据
     * @param false/true   $json    false $data数组格式  true $data json格式
     * @param integer      $timeout 连接超时时间
     * @return  返回json数据
     */
    static function curlPost($url, $data = null, $json = FALSE, $timeout = 30){
        //创建了一个curl会话资源，成功返回一个句柄；
        $curl = curl_init();
        //设置url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置为FALSE 禁止 cURL 验证对等证书（peer’s certificate）
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        //设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            //设置请求为POST
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout); //最长的可忍受的连接时间
            //设置POST的数据域
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            if($json){
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Content-Length: ' . strlen($data)
                    )
                );
            }
        }
        //设置是否将响应结果存入变量，1是存入，0是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        //然后将响应结果存入变量
        $output = curl_exec($curl);
        //关闭这个curl会话资源
        curl_close($curl);
        return $output;
    }

    /**
     * 函数的含义说明：CURL发送get请求    获取数据
     *
     * @access public
     * @param str $url 发送接口地址  https://api.douban.com/v2/movie/in_theaters?city=广州&start=0&count=10
     * @return  返回json数据
     */
    static function curlGet($url){

        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        $output = curl_exec($curl);     //返回api的json对象
        //关闭URL请求
        curl_close($curl);
        return $output;    //返回json对象
    }


}
