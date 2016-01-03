<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/2
 * Time: 19:27
 */
define("IS_INITPHP", '');
require "GenCaptcha.php";
$img = new seccodeInit();
if($img->checkCode($_POST['code'])){
    echo "Pass!";
}else{
    echo "Error";
}