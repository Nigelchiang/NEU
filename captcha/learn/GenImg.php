<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/1
 * Time: 22:56
 */
//$arr = range('A', 'z');
//foreach ($arr as $letter) {
//    echo "'{$letter}'=>'',"."<br/>";
//}
//$decode = '0000001111111111111100000011111111111111000000001100001100000000000110000011000000000001100000110000000000011100011100000000000011111110000000000000011111000000';
//$p      = '0000001111111111111100000011111111111111000000001100001100000000000110000011000000000001100000110000000000011100011100000000000011111110000000000000011111000000';
//$b      = '0011111111111111000000111111111111110000000000001100001100000000000110000011000000000001100000110000000000011100011100000000000011111110000000000000011111000000';
//similar_text($decode, $p,$perscent);
//echo $perscent."\n";
//similar_text($decode, $b, $perscent);
//echo $perscent;
//print_r(gd_info());
//$im = imagecreatefromjpeg("captcha-1.jpg");
//if ($im && imagefilter($im, IMG_FILTER_GRAYSCALE)) {
//    header("content-type:image/jpeg");
//    imagejpeg($im,"gray.jpg");
//} else {
//    echo "failed";
//}
//imagedestroy($im);
//echo __FILE__ . "\n";
//echo __DIR__ . "\n";
//echo __LINE__ . "\n";
////dir();
//echo (dirname(__FILE__) === __DIR__) . "\n";
//$handle = opendir(__DIR__);
//while ($filename = readdir($handle)) {
//    if (is_dir($filename)) {
//    }
//    echo $filename."\n";
//}
define("IS_INITPHP", '');
require "GenCaptcha.php";
$img = new seccodeInit();
$img->getcode();
?>