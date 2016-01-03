<?php

/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/1
 * Time: 20:46
 */
class Captcha {

    /**
     * @var array 二值化后的二位数组
     */
    private $data;
    /**
     * @var array 图像大小
     */
    private $size;

    /**
     * @var string 图片的文件名
     */
    //private $filename;
    /**
     * @var resource 图片资源
     */
    private $img;
    /**
     * @var array 识别出的四个字符
     */
    private $letters;
    /**
     * @var array 字符库
     * 没有l/o/O
     */
    public static $keys = array(
        '0' => '00000011111100000000000011111111110000000001110000001110000000111000000000110000001100000000001100000011000000000011000000110000000001110000000111000000111000000000111111111100000000000111111100000000',
        '1' => '000011000000001100000001100000000011000000111111111111110000001111111111111100000000000000000011000000000000000000110000',
        '2' => '000011000000111100000001110000011111000000011000001110110000001100000111001100000011000001100011000000110000110000110000001110011100001100000001111110000011000000001111000000110000',
        '3' => '0001100000001100000000011000000001100000001100001000001100000011000110000011000000110001110000110000001110011100011100000001111101111110000000001110001111000000',
        '4' => '00000000001100000000000000000111000000000000000111110000000000000011001100000000000011110011000000000001100010110000000000111111111111110000001111111111111100000000000000110000000000000000001100000000',
        '5' => '001111111100011000000011111111000110000000110001100000110000001100110000001100000011001100000011000000110011000000110000001100111000011000000011000111111110000000110000111110000000',
        '6' => '000000001111110000000000001111111110000000001111110001110000000111001100001100000011100011000011000000110000110000110000000000001110011100000000000001111110000000000000001111000000',
        '7' => '00110000000000000000001100000000000000000011000000000011000000110000000111100000001100000111100000000011000111100000000000110111100000000000001111000000000000000011100000000000000000110000000000000000',
        '8' => '000011100011110000000001111101111110000000111011110001110000001100011000001100000011000110000011000000110001100000110000001100111100011100000001111111111110000000001110011111000000',
        '9' => '000011111000000000000001111111000001100000111000111000011000001100000110001100000011000001100111000000110000011011100000001110001111110000000001111111111000000000000111110000000000',
        'A' => '0000000000000011000000000000000111110000000000000011110000000000000111110000000000000111111100000000000011100110000000000011100001100000000000111111111000000000000000111111110000000000000000111111000000000000000000110000',
        'B' => '001111111111111100000011111111111111000000110000011000110000001100000110001100000011000001100011000000011000111001100000000111111011011000000000111100111100000000000000000111000000',
        'C' => '00000000111110000000000000111111111000000000011110000110000000001110000000110000000110000000001100000011000000000011000000110000000000110000001100000000011000000011100000001110000000111000000011000000',
        'D' => '0011111111111111000000111111111111100000001100000000001100000001100000000011000000011000000000110000000011000000001100000000110000000011000000000110000001100000000000110000111000000000000111111100000000000000111100000000',
        'E' => '001111111111111000000011111111111111000000110000110000110000001100001100001100000011000011000011000000110000110000110000001100001100001100000011000011000011000000110000000000000000',
        'F' => '001111111111111100000011111111111111000000110000111000000000001100001100000000000011000011000000000000110000110000000000001100001100000000000011000011000000000000010000000000000000',
        'G' => '0000000011111100000000000011111111100000000011110000011100000001110000000011000000011000011000110000001100000110001100000011000011000011000000110000110001100000000110001101110000000001100011111000000000000000111000000000',
        'H' => '001111111111111100000011111111111111000000000000011000000000000000000110000000000000000001100000000000000000011000000000000000001100000000000000000011000000000000000000110000000000000000001100000000000011111111111111000000111111111111110000',
        'I' => '0011000000000011000000110000000000110000001100000000001100000011111111111111000000111111111111110000001100000000001100000011000000000011000000110000000000110000',
        'J' => '0000000000011100000000000000000111100000001100000000011000000011000000000011000000110000000000110000001100000000011100000011111111111111000000111111111111100000001100000000000000000011000000000000000000110000000000000000',
        'K' => '001111111111111100000011111111111111000000000000111000000000000000110011000000000000011000011000000000001100000011000000000110000000011000000011000000000011000000000000000000110000',
        'L' => '0011111111111111000000111111111111110000000000000000001100000000000000000011000000000000000000110000000000000000001100000000000000000011000000000000000000110000',
        'M' => '0000000000001111000000000001111111110000000111111111000000000001111000000000000000111111111110000000000000011111011100000000000000000111000000000000001111110000000000011110100000000000111111000000000000111110000000000000001111111111100000000000000011111111000000000000000000110000',
        'N' => '001111111111111100000011111111111111000000001100000000000000000001110000000000000000001110000000000000000000110000000000000000000110000000000000000000111000000000000000000011000000000000000000011000000011111111111111000000011111111111110000',
        'O' => '',
        'P' => '00111111111111110000001111111111111100000011000001101000000000110000011000000000001110001110000000000001111111000000000000001111100000000000',
        'Q' => '0000000111110000000000000111111110000000000011100001110000000001110000000110000000011000000001110000001100000000001100000011000000011011000000110000000111110000001100000000111100000011000000000111000000011000000000111000000111100000111111000000111111111100111000000001111110000111',
        'R' => '00111111111111110000001111111111111100000011000000111000000000110000001100000000001110000011100000000011100001111100000000011100011111100000000011111100111000000000001110000111000000000000000000110000',
        'S' => '00000000000001100000000001111000011100000000111110000011100000011001110000011000001100001100000110000011000011000001100000110000110000011000001100001110001100000001000001111110000000000000001111000000',
        'T' => '00110000000000000000001100000000000000000011000000000000000000110000000000000000001111111111111100000011111111111111000000110000000000000000001100000000000000000011000000000000000000110000000000000000',
        'U' => '0011111111110000000000111111111111000000000000000000111000000000000000000111000000000000000000110000000000000000001100000000000000000011000000000000000001110000000000000001111000000011111111111100000000111111111000000000',
        'V' => '00111000000000000000001111111100000000000000001111111100000000000000001111110000000000000000011100000000000000111111000000000001111111000000000011111100000000000011111000000000000000110000000000000000',
        'W' => '0011100000000000000000111111000000000000000001111111000000000000000011111111000000000000000001110000000000000011111100000000001111110000000000011111110000000000001111000000000000000011111111111110000000000011111111110000000000000000001100000000000000011111000000000000111111000000',
        'X' => '000000000000001100000011000000000111000000111000000011100000000111100001110000000000011101110000000000000011111000000000000000011110000000000000001111110000000000000111001110000000000111000000111000000011100000000111000000110000000000110000',
        'Y' => '0011000000000000000000111000000000000000000011100000000000000000011110000001100000000001110001111000000000000111111000000000000011111000000000000011111000000000000011111000000000000011110000000000000000110000000000000000',
        'Z' => '001100000000011100000011000000001111000000110000000110110000001100000011001100000011000011100011000000110001110000110000001100111000001100000011011000000011000000111100000000110000001110000000001100000011100000000011000000110000000000110000',
        'a' => '0000000000111100000000000000111111100000000000001100001100000000000110000011000000000001100000110000000000011111111000000000000111111110000000000000000000110000',
        'b' => '0011111111111111000000111111111111110000000000001100001100000000000110000011000000000001100000110000000000011100011100000000000011111110000000000000011111000000',
        'c' => '00000000001111000000000000001111111000000000000111000011000000000001100000110000000000011000001100000000000110000111000000000000110001100000',
        'd' => '0000000001111100000000000000111111100000000000011100011100000000000110000011000000000001100000110000000000001000011100000011111111111111000000111111111111110000',
        'e' => '0000000000111100000000000000111111100000000000011101111100000000000110010011000000000001101100110000000000011011001100000000000111100010000000000000110001100000',
        'f' => '00000001100000000000000000011000000000000000111111111111000000011111111111110000001110011000000000000011000110000000000000110001100000000000',
        'g' => '0000000000111100000100000000111111100001000000001100001100010000000110000011000100000001100000111001000000011000110001110000000111111111111100000000111111111100',
        'h' => '00111111111111110000001111111111111100000000000011000000000000000001100000000000000000011000000000000000000111111111000000000000111111110000',
        'i' => '0001100111111111000000011001111111110000',
        'j' => '000000000000000001100000000000000000011100000000000000000001000000000000000000010001100111111111111100011001111111111111',
        'k' => '0011111111111111000000111111111111110000000000000001100000000000000000110000000000000000011110000000000000001100110000000000000110000111000000000001000000110000',
        'l' => '',
        'm' => '000000011111111100000000000111111111000000000000011000000000000000001100000000000000000110000000000000000001111111110000000000001111111100000000000011000000000000000001100000000000000000011000000000000000000111111111000000000000111111110000',
        'n' => '00000001111111110000000000011111111100000000000011000000000000000001100000000000000000011000000000000000000111111111000000000000111111110000',
        'o' => '',
        'p' => '0000001111111111111100000011111111111111000000001100001100000000000110000011000000000001100000110000000000011100011100000000000011111110000000000000011111000000',
        'q' => '00000000001111000000000000001111111000000000000011000011000000000001100000110000000000011000011100000000000111111111111100000001111111111111',
        'r' => '00000001111111110000000000011111111100000000000011000000000000000000100000000000000000011000000000000000000111100000000000000001111000000000',
        's' => '00000000011100110000000000001111001100000000000011011011000000000000110110110000000000011001101100000000000111011111000000000001110011100000',
        't' => '000000011000000000000000000110000000000000001111111111110000000011111111111100000000000110000000000000000001100000000000',
        'u' => '00000001111111100000000000011111111100000000000000000011000000000000000000110000000000000000001100000000000111111111000000000001111111110000',
        'v' => '0000000110000000000000000001111100000000000000000111111000000000000000001111000000000000000011110000000000000011110000000000000111110000000000000001100000000000',
        'w' => '0000000111110000000000000001111111100000000000000001111100000000000000001111000000000000111110000000000000011111000000000000000011111111000000000000000001110000000000000011111100000000000111111100000000000001110000000000',
        'x' => '000000011000001100000000000111000111000000000000111011100000000000000111100000000000000000111000000000000000011111000000000000001110111000000000000111000111000000000001100000110000',
        'y' => '000000011000000000000000000111100000000000000000111110000011000000000011111011110000000000000111110000000000000111100000000000000111100000000000000111100000000000000001100000000000',
        'z' => '0000000110000011000000000001100001110000000000011000111100000000000110011111000000000001111110110000000000011111001100000000000111000011000000000001100000110000',);
    /**
     * @var string 最终识别结果
     */
    public $result;

    function __construct(&$file) {

        //if (is_resource($file)) {
        $this->img = imagecreatefromstring($file);
        //} else {
        //    $this->filename = $file;
        //    $this->img      = imagecreatefromjpeg($this->filename);
        //}
        $this->toBinary();
        //imagejpeg($tmp->img, "step1.jpg");
        $this->filter();
        //imagejpeg($tmp->img, "step2.jpg");
        $this->spilt();
        $this->compare();
    }

    /**
     * 二值化
     */
    function toBinary() {

        //$this->size = getimagesize($this->filename);
        $this->size[0] = imagesx($this->img);
        $this->size[1] = imagesy($this->img);
        //$white      = imagecolorallocate($this->img, 255, 255, 255);
        //$black      = imagecolorallocate($this->img, 0, 0, 0);

        //一行一行地扫描
        for ($i = 0; $i < $this->size[0]; ++$i) {
            for ($j = 0; $j < $this->size[1]; ++$j) {
                $rgb      = imagecolorat($this->img, $i, $j);
                $rgbarray = imagecolorsforindex($this->img, $rgb);
                // =========================================================
                // 任何验证码的数字和字母部分为了和验证码图片背景有所区别
                // 都必须对文字和背景图片的RGB进行区分，下面的值是我根据
                // 验证码的图片进行区分的，您可以分析您的图片，找到如下规律
                // =========================================================
                if ($rgbarray['red'] < 125 || $rgbarray['green'] < 125
                    || $rgbarray['blue'] < 125
                ) {
                    //imagesetpixel($this->img, $i, $j, $black);
                    $this->data[$i][$j] = 1;
                } else {
                    $this->data[$i][$j] = 0;
                    //imagesetpixel($this->img, $i, $j, $white);
                }
            }
        }
        imagedestroy($this->img);
    }

    /**
     * 去除噪点
     */
    function filter() {

        //$white = imagecolorallocate($this->img, 255, 255, 255);
        for ($i = 0; $i < $this->size[0]; ++$i) {
            for ($j = 0; $j < $this->size[1]; ++$j) {
                $num = 0;
                if ($this->data[$i][$j] == 1) {
                    // 上
                    if (isset($this->data[$i - 1][$j])) {
                        $num += $this->data[$i - 1][$j];
                    }
                    // 下
                    if (isset($this->data[$i + 1][$j])) {
                        $num += $this->data[$i + 1][$j];
                    }
                    // 左
                    if (isset($this->data[$i][$j - 1])) {
                        $num += $this->data[$i][$j - 1];
                    }
                    // 右
                    if (isset($this->data[$i][$j + 1])) {
                        $num += $this->data[$i][$j + 1];
                    }
                    // 上左
                    if (isset($this->data[$i - 1][$j - 1])) {
                        $num += $this->data[$i - 1][$j - 1];
                    }
                    // 上右
                    if (isset($this->data[$i - 1][$j + 1])) {
                        $num += $this->data[$i - 1][$j + 1];
                    }
                    // 下左
                    if (isset($this->data[$i + 1][$j - 1])) {
                        $num += $this->data[$i + 1][$j - 1];
                    }
                    // 下右
                    if (isset($this->data[$i + 1][$j + 1])) {
                        $num += $this->data[$i + 1][$j + 1];
                    }
                    //如果num==0，则为噪点
                    if ($num == 0) {
                        $this->data[$i][$j] = 0;
                        //imagesetpixel($this->img, $i, $j, $white);
                    }
                }
            }
        }
    }

    /**
     * 分割，取出四个字符的01序列
     */
    function spilt() {

        $inLetter = false;
        $letter   = '';
        for ($i = 0; $i < $this->size[0]; ++$i) {
            //一列的和
            $colSum = 0;
            //一列拼成的字符串
            $col = '';

            for ($j = 0; $j < $this->size[1]; ++$j) {
                $colSum += $this->data[$i][$j];
                $col .= $this->data[$i][$j];
            }
            //字符部分列 未发生粘连:左边按照最大的字符切割
            if ($colSum != 0 && strlen($letter) < 280) {
                $inLetter = true;
                $letter .= $col;
            }
            if ($colSum != 0 && strlen($letter) >= 280) {
                $inLetter        = true;
                $this->letters[] = $letter;
                $letter          = '';
            }

            //到字符最后一列,或者到达图片的最右侧
            if (($colSum == 0 && $inLetter) || ($inLetter && $i == $this->size[0] - 1)) {
                $this->letters[] = $letter;
                $letter          = '';
                $inLetter        = false;
            }
        }
    }

    /**
     * 比较，匹配最佳字符
     */
    function compare() {

        $result = '';
        foreach ($this->letters as $letter) {
            $max       = 0.0;
            $character = '';
            foreach (self::$keys as $key => $value) {
                $percent = 0.0;
                similar_text($value, $letter, $percent);
                if (intval($percent) > $max) {
                    $max       = $percent;
                    $character = $key;
                    if (intval($percent) > 99) {
                        break;
                    }
                }
            }
            $result .= $character;
        }
        $this->result = $result;
    }

    /**
     * 获取字符库
     */
    function train() {

    }

    /**
     * 测试正确率
     */
    function test() {

    }
}
