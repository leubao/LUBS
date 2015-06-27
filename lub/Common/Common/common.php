<?php
// +----------------------------------------------------------------------
// | LubTMP
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

/**
 * 系统缓存缓存管理
 * @param mixed $name 缓存名称
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function cache($name, $value = '', $options = null) {
    static $cache = '';
    if (empty($cache)) {
        $cache = \Libs\System\Cache::getInstance();
    }
    // 获取缓存
    if ('' === $value) {
        if (false !== strpos($name, '.')) {
            $vars = explode('.', $name);
            $data = $cache->get($vars[0]);
            return is_array($data) ? $data[$vars[1]] : $data;
        } else {
            return $cache->get($name);
        }
    } elseif (is_null($value)) {//删除缓存
        return $cache->remove($name);
    } else {//缓存数据
        if (is_array($options)) {
            $expire = isset($options['expire']) ? $options['expire'] : NULL;
        } else {
            $expire = is_numeric($options) ? $options : NULL;
        }
        return $cache->set($name, $value, $expire);
    }
}

/**
 * 调试，用于保存数组到txt文件 正式生产删除
 * 用法：array2file($info, SITE_PATH.'post.txt');
 * @param type $array
 * @param type $filename
 */
function array2file($array, $filename) {
    if (defined("APP_DEBUG") && APP_DEBUG) {
        //修改文件时间
        file_exists($filename) or touch($filename);
        if (is_array($array)) {
            $str = var_export($array, TRUE);
        } else {
            $str = $array;
        }
        return file_put_contents($filename, $str);
    }
    return false;
}

/**
 * 返回LubTMP对象
 * @return Object
 */
function LubTMP() {
    return \Common\Controller\LubTMP::app();
}

/**
 * 快捷方法取得服务
 * @param type $name 服务类型
 * @param type $params 参数
 * @return type
 */
function service($name, $params = array()) {
    return \Libs\System\Service::getInstance($name, $params);
}

/**
 * 生成上传附件验证
 * @param $args   参数
 */
function upload_key($args) {
    return md5($args . md5(C("AUTHCODE") . $_SERVER['HTTP_USER_AGENT']));
}

/**
 * 检查模块是否已经安装
 * @param type $moduleName 模块名称
 * @return boolean
 */
function isModuleInstall($moduleName) {
    $appCache = cache('Module');
    if (isset($appCache[$moduleName])) {
        return true;
    }
    return false;
}

/**
 * 产生一个指定长度的随机字符串,并返回给用户 
 * @param type $len 产生字符串的长度
 * @param $type int 生成类型
 * @return string 随机字符串
 */
function genRandomString($len = 6,$type = null) {
    if($type == '1'){
        $chars = array("0", "1", "2","3", "4", "5", "6", "7", "8", "9");
    }else{
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
    }  
    $charsLen = count($chars) - 1;
    // 将数组打乱 
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * 获取模型数据
 * @param type $modelid 模型ID
 * @param type $field 返回的字段，默认返回全部，数组
 * @return boolean
 */
function getModel($modelid, $field = '') {
    if (empty($modelid)) {
        return false;
    }
    $key = 'getModel_' . $modelid;
    $cache = S($key);
    if ($cache === 'false') {
        return false;
    }
    if (empty($cache)) {
        //读取数据
        $cache = M('Model')->where(array('modelid' => $modelid))->find();
        if (empty($cache)) {
            S($key, 'false', 60);
            return false;
        } else {
            S($key, $cache, 3600);
        }
    }
    if ($field) {
        return $cache[$field];
    } else {
        return $cache;
    }
}

/**
 * 检测一个数据长度是否超过最小值
 * @param type $value 数据
 * @param type $length 最小长度
 * @return type 
 */
function isMin($value, $length) {
    return mb_strlen($value, 'utf-8') >= (int) $length ? true : false;
}

/**
 * 检测一个数据长度是否超过最大值
 * @param type $value 数据
 * @param type $length 最大长度
 * @return type 
 */
function isMax($value, $length) {
    return mb_strlen($value, 'utf-8') <= (int) $length ? true : false;
}

/**
 * 取得文件扩展
 * @param type $filename 文件名
 * @return type 后缀
 */
function fileext($filename) {
    $pathinfo = pathinfo($filename);
    return $pathinfo['extension'];
}

/**
 * 对 javascript escape 解码
 * @param type $str 
 * @return type
 */
function unescape($str) {
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f)
                $ret .= chr($val);
            else
            if ($val < 0x800)
                $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
            else
                $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else
        if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        } else
            $ret .= $str[$i];
    }
    return $ret;
}

/**
 * 字符截取
 * @param $string 需要截取的字符串
 * @param $length 长度
 * @param $dot
 */
function str_cut($sourcestr, $length, $dot = '...') {
    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr); //字符串的字节数 
    while (($n < $length) && ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码 
        if ($ascnum >= 224) {//如果ASCII位高与224，
            $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符         
            $i = $i + 3; //实际Byte计为3
            $n++; //字串长度计1
        } elseif ($ascnum >= 192) { //如果ASCII位高与192，
            $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符 
            $i = $i + 2; //实际Byte计为2
            $n++; //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) { //如果是大写字母，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        } else {//其他情况下，包括小写字母和半角标点符号，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1;            //实际的Byte数计1个
            $n = $n + 0.5;        //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length > strlen($returnstr)) {
        $returnstr = $returnstr . $dot; //超过长度时在尾处加上省略号
    }
    return $returnstr;
}
/**
 * 取得URL地址中域名部分
 * @param type $url 
 * @return \url 返回域名
 */
function urlDomain($url) {
    if ($url) {
        $pathinfo = parse_url($url);
        return $pathinfo['scheme'] . "://" . $pathinfo['host'] . "/";
    }
    return false;
}

/**
 * 获取当前页面完整URL地址
 * @return type 地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 返回附件类型图标
 * @param $file 附件名称
 * @param $type png为大图标，gif为小图标
 */
function file_icon($file, $type = 'png') {
    $ext_arr = array('doc', 'docx', 'ppt', 'xls', 'txt', 'pdf', 'mdb', 'jpg', 'gif', 'png', 'bmp', 'jpeg', 'rar', 'zip', 'swf', 'flv');
    $ext = fileext($file);
    if ($type == 'png') {
        if ($ext == 'zip' || $ext == 'rar')
            $ext = 'rar';
        elseif ($ext == 'doc' || $ext == 'docx')
            $ext = 'doc';
        elseif ($ext == 'xls' || $ext == 'xlsx')
            $ext = 'xls';
        elseif ($ext == 'ppt' || $ext == 'pptx')
            $ext = 'ppt';
        elseif ($ext == 'flv' || $ext == 'swf' || $ext == 'rm' || $ext == 'rmvb')
            $ext = 'flv';
        else
            $ext = 'do';
    }
    $config = cache('Config');
    if (in_array($ext, $ext_arr)) {
        return $config['siteurl'] . 'statics/images/ext/' . $ext . '.' . $type;
    } else {
        return $config['siteurl'] . 'statics/images/ext/blank.' . $type;
    }
}

/**
 * 根据文件扩展名来判断是否为图片类型
 * @param type $file 文件名
 * @return type 是图片类型返回 true，否则返回 false
 */
function isImage($file) {
    $ext_arr = array('jpg', 'gif', 'png', 'bmp', 'jpeg', 'tiff');
    //取得扩展名
    $ext = fileext($file);
    return in_array($ext, $ext_arr) ? true : false;
}

/**
 * 对URL中有中文的部分进行编码处理
 * @param type $url 地址 http://www.chengde360.com/s?wd=博客
 * @return type ur;编码后的地址 http://www.chengde360.com/s?wd=%E5%8D%9A%20%E5%AE%A2
 */
function cn_urlencode($url) {
    $pregstr = "/[\x{4e00}-\x{9fa5}]+/u"; //UTF-8中文正则
    if (preg_match_all($pregstr, $url, $matchArray)) {//匹配中文，返回数组
        foreach ($matchArray[0] as $key => $val) {
            $url = str_replace($val, urlencode($val), $url); //将转译替换中文
        }
        if (strpos($url, ' ')) {//若存在空格
            $url = str_replace(' ', '%20', $url);
        }
    }
    return $url;
}

/**
 * 邮件发送
 * @param type $address 接收人 单个直接邮箱地址，多个可以使用数组
 * @param type $title 邮件标题
 * @param type $message 邮件内容
 */
function SendMail($address, $title, $message) {
    $config = cache('Config');
    import('PHPMailer');
    try {
        $mail = new \PHPMailer();
        $mail->IsSMTP();
        // 设置邮件的字符编码，若不指定，则为'UTF-8'
        $mail->CharSet = C("DEFAULT_CHARSET");
        $mail->IsHTML(true);
        // 添加收件人地址，可以多次使用来添加多个收件人
        if (is_array($address)) {
            foreach ($address as $k => $v) {
                if (is_array($v)) {
                    $mail->AddAddress($v[0], $v[1]);
                } else {
                    $mail->AddAddress($v);
                }
            }
        } else {
            $mail->AddAddress($address);
        }
        // 设置邮件正文
        $mail->Body = $message;
        // 设置邮件头的From字段。
        $mail->From = $config['mail_from'];
        // 设置发件人名字
        $mail->FromName = $config['mail_fname'];
        // 设置邮件标题
        $mail->Subject = $title;
        // 设置SMTP服务器。
        $mail->Host = $config['mail_server'];
        // 设置为“需要验证”
        if ($config['mail_auth']) {
            $mail->SMTPAuth = true;
        } else {
            $mail->SMTPAuth = false;
        }
        // 设置用户名和密码。
        $mail->Username = $config['mail_user'];
        $mail->Password = $config['mail_password'];
        return $mail->Send();
    } catch (phpmailerException $e) {
        return $e->errorMessage();
    } 
}
// +----------------------------------------------------------------------
// | LubTMP 汉字转拼音
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------
define('CODETABLEDIR', COMMON_PATH . 'Data/');

/**
 * gbk转拼音
 * @param $txt
 */
function gbk_to_pinyin($txt) {
    $l = strlen($txt);
    $i = 0;
    $pyarr = array();
    $py = array();
    $filename = CODETABLEDIR . 'gb-pinyin.table';
    $fp = fopen($filename, 'r');
    while (!feof($fp)) {
        $p = explode("-", fgets($fp, 32));
        $pyarr[intval($p[1])] = trim($p[0]);
    }
    fclose($fp);
    ksort($pyarr);
    while ($i < $l) {
        $tmp = ord($txt[$i]);
        if ($tmp >= 128) {
            $asc = abs($tmp * 256 + ord($txt[$i + 1]) - 65536);
            $i = $i + 1;
        } else
            $asc = $tmp;
        $py[] = asc_to_pinyin($asc, $pyarr);
        $i++;
    }
    return $py;
}

/**
 * Ascii转拼音
 * @param $asc
 * @param $pyarr
 */
function asc_to_pinyin($asc, &$pyarr) {
    if ($asc < 128)
        return chr($asc);
    elseif (isset($pyarr[$asc]))
        return $pyarr[$asc];
    else {
        foreach ($pyarr as $id => $p) {
            if ($id >= $asc)
                return $p;
        }
    }
}
/*计算时间差并返回差多少天、时、分、秒 
* @param int $begin_time
* @param int $end_time
* @return string
*/
function timediff($begin_time,$end_time) { 
      if($begin_time < $end_time){ 
         $starttime = $begin_time; 
         $endtime = $end_time; 
      } 
      else{ 
         $starttime = $end_time; 
         $endtime = $begin_time; 
      } 
      $timediff = $endtime-$starttime; 
      $days = intval($timediff/86400); 
      $remain = $timediff%86400; 
      $hours = intval($remain/3600); 
      $remain = $remain%3600; 
      $mins = intval($remain/60); 
      $secs = $remain%60; 
      $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs); 
      return $res; 
}
/*************************************票务系统*******************************************************/

/**
 * 格式化金额
 *
 * @param int $money
 * @param int $len
 * @param string $sign
 * @return string
 */
function format_money($money, $len=2, $sign='￥'){
    $negative = $money > 0 ? '' : '-';
    $int_money = intval(abs($money));
    $len = intval(abs($len));
    $decimal = '';//小数
    if ($len > 0) {
        $decimal = '.'.substr(sprintf('%01.'.$len.'f', $money),-$len);
    }
    $tmp_money = strrev($int_money);
    $strlen = strlen($tmp_money);
    for ($i = 3; $i < $strlen; $i += 3) {
        $format_money .= substr($tmp_money,0,3).',';
        $tmp_money = substr($tmp_money,3);
    }
    $format_money .= $tmp_money;
    $format_money = strrev($format_money);
    return $sign.$negative.$format_money.$decimal;
}