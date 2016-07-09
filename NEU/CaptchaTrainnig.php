<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/13
 * Time: 20:22
 */
if (isset($_POST['1'])) {
    $db = new mysqli('localhost', 'nigel', 'nigel', "captcha") or die($db->error);
    $data = explode("!", $_POST['data']);
    for ($i = 0; $i < 4; ++$i) {
        if ($_POST[$i] !== '') {
            $query = "insert into ecard(letter,value) VALUES('{$_POST[$i]}','{$data[$i]}')";
            $group = "select value from ecard WHERE letter='{$_POST[$i]}'";
            $result = $db->query($group) or die($db->error);
            $group = $result->fetch_all(MYSQLI_NUM);

            if (!in_array($data[$i], $group)) {

                $db->query($query) or die($db->error);
            }
        }
    }
    $db->close();
}
include "autoload.php";
//训练模式
define("TRAINNING", true);
//$ecard = new Nigel\NEU\Ecard();
//$ecard->train();

//$img   = &imagecreatefromjpeg("trainningCaptcha.jpg");
$img   = &imagecreatefromjpeg("11.jpg");
$ecard = new Nigel\NEU\Captcha_ecard($img);

$data = join("!", $ecard->letters)
?>

<h1>验证码</h1>
<!--<img src="trainningCaptcha.jpg" alt="获取的验证码">-->
<img src="11.jpg" alt="获取的验证码">
<h1>处理后</h1>
<img src="trainningCaptcha-after.jpg" alt="处理之后的结果">
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    第一个<label><input type="text" name="0" autofocus></label>
    第二个<label><input type="text" name="1"></label>
    第三个<label><input type="text" name="2"></label>
    第四个<label><input type="text" name="3"></label>
    <input type="hidden" name="data" value="<?php echo $data ?>">
    <input type="submit" value="提交">
</form>