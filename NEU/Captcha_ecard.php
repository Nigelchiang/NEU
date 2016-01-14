<?php

/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/5
 * Time: 2:46
 */
namespace Nigel\NEU;

include "autoload.php";

class Captcha_ecard {

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
    public $img;
    /**
     * @var array 识别出的四个字符
     */
    public $letters;
    /**
     * @var array 字符库
     * 有的验证码使用多种字体，则每个字符对应多个01序列
     */
    public $keys = array();
    /**
     * @var string 最终识别结果
     */
    public $result;

    /**
     * Captcha_ecard constructor.
     *
     * @param $file resource 只接受使用imagecreatefromxxx创建的资源
     */
    public function __construct(&$file) {

        if (is_resource($file)) {
            $this->img = &$file;
        } else {
            die("Captcha constructor:Please pass a img resourse");
        }
        $this->getKeys();
        $this->toBinary();
        //如果处于训练模式
        if (defined('TRAINNING') && TRAINNING) {
            imagejpeg($this->img, "trainningCaptcha-after.jpg");
            imagedestroy($this->img);
        }
        $this->filter();
        $this->spilt();
        if (!defined('TRAINNING')) {
            $this->compare();
        }

    }

    /**
     * 从数据库中获取所有的key
     */
    private function getKeys() {

        $query = "select * from ecard";
        $db = new \mysqli('localhost', 'nigel', 'nigel', 'captcha') or die($db->error);
        $result = $db->query($query) or die($db->error);
        $this->keys = $result->fetch_all(MYSQLI_ASSOC) or die($db->error);
    }

    /**
     * 二值化
     */
    public function toBinary() {

        $this->size[0] = imagesx($this->img);
        $this->size[1] = imagesy($this->img);
        $white         = imagecolorallocate($this->img, 255, 255, 255);
        $black         = imagecolorallocate($this->img, 0, 0, 0);

        //一列一列地扫描
        for ($i = 0; $i < $this->size[0]; ++$i) {
            for ($j = 0; $j < $this->size[1]; ++$j) {
                $rgb      = imagecolorat($this->img, $i, $j);
                $rgbarray = imagecolorsforindex($this->img, $rgb);

                // 任何验证码的数字和字母部分为了和验证码图片背景有所区别
                // 都必须对文字和背景图片的RGB进行区分,你可以分析你的图片，找到规律
                if ($rgbarray['red'] < 120
                    || $rgbarray['green'] < 120
                    || $rgbarray['blue'] < 120
                ) {
                    defined('TRAINNING') && imagesetpixel($this->img, $i, $j, $black);
                    $this->data[$i][$j] = 1;
                } else {
                    $this->data[$i][$j] = 0;
                    defined('TRAINNING') && imagesetpixel($this->img, $i, $j, $white);
                }
            }
        }

    }

    /**
     * 去除噪点
     */
    public function filter() {

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
     * 分隔
     */
    public function spilt() {

        $data = array();
        $tmp  = array();
        //去除所有列中的空行，每个字母的所有列为data中的一项
        //data第一维个数为验证码字母的个数
        //如果有字母粘连情况，则应控制tmp的最大项数，在达到最大数之后强制向data中添加tmp这一项，tmp清零
        foreach ($this->data as &$col) {
            if (array_sum($col) == 0) {
                if (isset($in) && $in) {
                    $in     = false;
                    $data[] = $tmp;
                    $tmp    = array();
                }
            } else {
                $in    = true;
                $tmp[] = $col;
            }
        }

        $chars = array();
        $tmp   = array();
        //将每个字符的每一个列数据依次移出一个组成一个新的行数组
        foreach ($data as &$letter) {
            while (count($letter[array_rand($letter)])) {
                $row = array();
                foreach ($letter as &$col) {
                    $row[] = array_shift($col);
                }
                if (array_sum($row) == 0) {
                    if (isset($in) && $in) {
                        $in      = false;
                        $chars[] = $tmp;
                        $tmp     = array();
                    }
                } else {
                    $in    = true;
                    $tmp[] = $row;
                }

            }

        }
        //组合数组的01序列
        foreach ($chars as $char) {
            $row = array();
            foreach ($char as $item) {
                $row[] = join('', $item);
            }
            $this->letters[] = join("", $row);
        }
    }

    /**
     * 比较，匹配最佳字符
     */
    public function compare() {

        $result = '';
        foreach ($this->letters as $letter) {
            $max       = 0.0;
            $character = '';
            foreach ($this->keys as $line) {
                $percent = 0.0;
                similar_text($line['value'], $letter, $percent);
                if (intval($percent) > $max) {
                    $max       = $percent;
                    $character = $line['letter'];
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
     * 测试正确率
     */
    public static function test() {

        (new Ecard())->train();
        $img = 'trainningCaptcha.jpg';

        $captcha = new Captcha_ecard(imagecreatefromjpeg($img));
        $result  = $captcha->result;
        echo '<img src=' . $img . '><p>resutl:' . $result . "</p>";
    }
}

Captcha_ecard::test();