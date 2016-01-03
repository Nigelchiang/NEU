<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/1
 * Time: 20:32
 */
/*
画图的一般步骤：
1.创建画布
2.创建颜料
3.画图或者写字
4.保存
5.销毁资源
*/
//创建画布
//$img=imagecreatetruecolor(300,200);
////创建颜料
//$bg=imagecolorallocate($img,30,255,255);
////画布填充颜色
//imagefill($img,0,0,$bg);
////保存图片
//if(imagepng($img,'./01.png')){
//    echo "图片创建成功";
//}
////销毁图片
//imagedestroy($img);
//简单验证码：

//创建图片
$im = imagecreatetruecolor(60, 20);
// 将背景设为蓝色
$blue = imagecolorallocate($im, 255, 255, 255);
//创建颜料
$imgcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
//填充背景颜色
imagefill($im, 0, 0, $blue);
//画干扰线
for ($i = 0; $i < 4; $i++) {
    imageline($im, rand(0, 20), 0, 100, rand(0, 60), $imgcolor);
}
//画噪点
for ($i = 0; $i < 100; $i++) {
    imagesetpixel($im, rand(0, 60), rand(0, 20), $imgcolor);
}
//写字符串
$str = substr(str_shuffle('ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'), 0, 4);
imagestring($im, 4, 10, 5, $str, $imgcolor);
//输出图片
header('content-type: image/png');
imagepng($im);
//销毁图片
imagedestroy($im);