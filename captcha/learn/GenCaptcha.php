<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/2
 * Time: 18:48
 */
if (!defined('IS_INITPHP')) {
    exit('Access Denied!');
}

class seccodeInit {

    private $width;
    private $height;
    private $type   = 0; // 0 字母+数字验证码
    private $time   = 3000; // 验证码过期时间(s)
    private $color  = null; // 验证码字体颜色
    private $im;
    private $length = 6; // 验证码长度
    private $warping; // 随机扭曲

    /*
    * 获取随机数值
    * @return string
    */
    private function get_random_val() {

        $i       = 0;
        $authnum = '';
        while ($i < $this->length) {
            mt_srand((double)microtime() * 1000000);
            $randnum = mt_rand(50, 90);
            if (!in_array($randnum, array(58, 59, 60, 61, 62, 63, 64, 73, 79))) {
                //将ASCII转为字符
                $authnum .= chr($randnum);
                $i++;
            }
        }
        session_start();
        $time                     = time();
        $checkcode                = md5(md5($authnum . 'initphpYzmsy' . $time));
        $key                      = $time . ',' . $checkcode . ',' . $authnum;
        $_SESSION['initphp_code'] = $key;

        return $authnum;
    }

    /*
    * 获取验证码图片
    * @param $width 宽
    * @param $height 高
    * @param $warping 字体随机扭曲开关 0=关，1=开
    * @return string
    */
    public function getcode($width = 140, $height = 40, $warping = 0) {

        $this->width   = $width;
        $this->height  = $height;
        $this->warping = $warping;
        if ($this->type < 2
            && function_exists('imagecreate')
            && function_exists('imagecolorset')
            && function_exists('imagecopyresized')
            && function_exists('imagecolorallocate')
            && function_exists('imagechar')
            && function_exists('imagecolorsforindex')
            && function_exists('imageline')
            && function_exists('imagecreatefromstring')
            && (function_exists('imagegif') || function_exists('imagepng') || function_exists('imagejpeg'))
        ) {
            $this->image();
        }
    }

    /*
    * 生成图片验证码
    * @return string
    */
    public function image() {

        $this->im = imagecreate($this->width, $this->height); // 设置图片背景大小
        imagecolorallocate($this->im, 243, 251, 254); // 设置背景
        $this->color = imagecolorallocate($this->im, mt_rand(1, 120), mt_rand(1, 120), mt_rand(1, 120)); // 验证码字体随机颜色
        $ttfPath     = dirname(__FILE__) . '/font/'; // 字体目录
        $dirs        = opendir($ttfPath);
        $seccodettf  = array();
        while ($entry = readdir($dirs)) {
            if ($entry != '.'
                && $entry != '..'
                && in_array(strtolower(addslashes(strtolower(substr(strrchr($entry, '.'), 1, 10)))),
                            array('ttf', 'ttc'))
            ) {
                $seccodettf[] = $ttfPath . $entry;
            }
        }
        $ttf  = $seccodettf[array_rand($seccodettf)]; // 随机一种字体
        $size = $this->type ? $this->width / 7 : $this->width / 6; // 字体大小
        imagettftext($this->im, $size, 0, 10, $size * 1.2, $this->color, $ttf, $this->get_random_val()); // 设置验证码字符
        //if ($this->warping) { // 随机扭曲
        //    $this->setWarping();
        //}
        $code = false;
        if (function_exists("imagepng")) {
            header("Content-type: image/png");
            $code = imagepng($this->im);
        } else if (function_exists("imagejpeg")) {
            header("Content-type: image/jpeg");
            $code = imagejpeg($this->im);
        } else if (function_exists("imagegif")) {
            header("Content-type: image/gif");
            $code = imagegif($this->im);
        }
        imagedestroy($this->im);

        return $code;
    }

    /*
    * 检查验证码
    * @param $code
    * @return bool
    */
    public function checkCode($code) {

        session_start();
        $secode = explode(',', $_SESSION['initphp_code']);
        $time   = time();

        // 检查时间是否过期
        if ($secode[0] > $time || $time - $secode[0] > $this->time) {
            return false;
        }

        //验证码密钥 双md5 后是否一致
        if ($secode[1] != md5(md5($code . 'initphpYzmsy' . $secode[0]))) {
            return false;
        }

        // 检查验证码字符串是否一致
        if ($code || ($code != $secode[2])) {
            return false;
        }

        return true;
    }
}