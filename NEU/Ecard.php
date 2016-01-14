<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/4
 * Time: 19:51
 */
namespace Nigel\NEU;

include "autoload.php";

class Ecard extends AAO {

    private $captchaUrl;
    private $loginUrl;
    private $infoUrl;
    private $photoUrl;
    /**
     * @var int curl错误次数
     */
    private $error = 0;

    /**
     * Ecard constructor.
     */
    function __construct() {

        $this->cookiefile = __DIR__ . '\\cookie-ecard.txt';

        $this->captchaUrl = "http://ecard.neu.edu.cn/SelfSearch/validateimage.ashx?0.75183199881576";
        $this->loginUrl   = "http://ecard.neu.edu.cn/SelfSearch/Login.aspx";
        $this->infoUrl    = "http://ecard.neu.edu.cn/SelfSearch/User/Home.aspx";
        $this->photoUrl   = "http://ecard.neu.edu.cn/SelfSearch/User/Photo.ashx";
    }

    protected function getCaptchaAndCookie() {

        $options = array(
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_REFERER        => "http://ecard.neu.edu.cn/SelfSearch/Index.aspx",
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36",
            CURLOPT_BINARYTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_COOKIEJAR      => $this->cookiefile,
        );
        $ch      = curl_init($this->captchaUrl);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        //如果curl出错，则error加一
        !($this->img = &imagecreatefromstring($result)) && $this->error++;
        curl_close($ch);
    }

    public function train() {

        $this->getCaptchaAndCookie();
        imagejpeg($this->img, './trainningCaptcha.jpg');
        imagedestroy($this->img);
    }

    protected function login($id, $pass, $captcha) {

        $postfields = array(
            '__LASTFOCUS'       => '',
            '__EVENTTARGET'     => 'btnLogin',
            '__EVENTARGUMENT'   => '',
            '__VIEWSTATE'       => '/wEPDwUKLTg0MDQ0NDk1OA8WAh4Hc3lzSW5mbzKSBQABAAAA/////wEAAAAAAAAADAIAAABPTmV3Y2FwZWMuVW5pdmVyc2FsU1MuRFRPLCBWZXJzaW9uPTEuMC4wLjAsIEN1bHR1cmU9bmV1dHJhbCwgUHVibGljS2V5VG9rZW49bnVsbAwDAAAAUk5ld2NhcGVjLlVuaXZlcnNhbFNTLkVudGl0eSwgVmVyc2lvbj0xLjAuMC4wLCBDdWx0dXJlPW5ldXRyYWwsIFB1YmxpY0tleVRva2VuPW51bGwFAQAAAChOZXdjYXBlYy5Vbml2ZXJzYWxTUy5EVE8uRFRPX09VVF9TWVNJTkZPBAAAAB48T1BFUkFUSU9OTU9ERT5rX19CYWNraW5nRmllbGQbRFRPX1dTUnVuUmVzdWx0K19yZXN1bHRDb2RlGkRUT19XU1J1blJlc3VsdCtfcmVzdWx0TXNnHURUT19XU1J1blJlc3VsdCtfRWNhcmRWZXJzaW9uBAEBBDNOZXdjYXBlYy5Vbml2ZXJzYWxTUy5FbnRpdHkuRW51bS5FbnVtX09QRVJBVElPTk1PREUDAAAAMk5ld2NhcGVjLlVuaXZlcnNhbFNTLkVudGl0eS5FbnVtLkVudW1fRWNhcmRWZXJzaW9uAwAAAAIAAAAF/P///zNOZXdjYXBlYy5Vbml2ZXJzYWxTUy5FbnRpdHkuRW51bS5FbnVtX09QRVJBVElPTk1PREUBAAAAB3ZhbHVlX18ACAMAAAABAAAABgUAAAABMQYGAAAADOaJp+ihjOaIkOWKnwX5////Mk5ld2NhcGVjLlVuaXZlcnNhbFNTLkVudGl0eS5FbnVtLkVudW1fRWNhcmRWZXJzaW9uAQAAAAd2YWx1ZV9fAAgDAAAAQAAAAAsWAgIDD2QWBAIDDw8WAh4HVmlzaWJsZWhkFgJmDw8WAh4EVGV4dAUIMDAwMDAwMDBkZAILDw8WBB4LTmF2aWdhdGVVcmwFI2h0dHBzOi8vZWNhcmQubmV1LmVkdS5jbi9zZWxmc2VhcmNoHwFnZGRkWqf1m/oTgXazfnKLTVBQyGEXotr2s3Z5+2AG7WJkULA=',
            '__EVENTVALIDATION' => '/wEWBgLMvZ+VCQKl1bKzCQK1qbSRCwLTtPqEDQLkysKABAKC3IeGDMpB5VR0RHidWtZmpI+UOvIergcFEyew23KOGmNb8w37',
            'txtUserName'       => $id,
            'txtPassword'       => $pass,
            'txtVaildateCode'   => $captcha,
            'hfIsManager'       => 0,
        );
        $options    = array(
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_COOKIEFILE     => $this->cookiefile,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER         => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => http_build_query($postfields),
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36',
            CURLOPT_REFERER        => 'https://ecard.neu.edu.cn/SelfSearch/Login.aspx',
        );

        $ch = curl_init($this->loginUrl);
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);
        $res || $this->error++;
        curl_close($ch);

        return strpos($res, 'var SetCardTypeInitStatus = function () {');
    }

    protected function getPhoto($id) {

        $options = array(
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_BINARYTRANSFER => 1,
            CURLOPT_REFERER        => $this->infoUrl,
            CURLOPT_COOKIEFILE     => $this->cookiefile,
        );
        $ch      = curl_init($this->photoUrl);
        curl_setopt_array($ch, $options);
        $img = curl_exec($ch);
        if (false !== $img) {
            $fp = fopen("..\\img\\ecard\\$id" . '.jpg', 'w');
            fwrite($fp, $img);
            fclose($fp);

            return true;
        } else {
            $this->error++;

            return false;
        }
    }

    protected function getInfo() {

        //todo
        parent::getInfo();
    }

    protected function insert() {

        //$info  = &$this->info;
        //$query = "INSERT INTO info(id, name, gender, social_id,school,enter_year,major,how_long,class,grade,type)"
        //         . "VALUES('$info[0]','$info[2]','$info[5]', '$info[16]', '$info[21]', '$info[22]','$info[23]', '$info[24]', '$info[25]', '$info[27]','$info[28]')";

        //mysqli_query($this->dbc, $query) or die('Error querying database: ' . mysqli_error($this->dbc));
    }

    public function run() {

        $this->dbc = mysqli_connect('localhost', 'nigel', 'nigel',
                                    'neu') or die("Error Connecting: " . mysqli_error($this->dbc));
        mysqli_set_charset($this->dbc, 'utf-8');

        set_time_limit(0); // 执行时间为无限制，php默认的执行时间是30秒，通过set_time_limit(0)可以让程序无限制的执行下去
        $interval = 1;
        //140240-1402999
        for ($id = 20144633; $id < 20144634; ++$id) {

            $this->getCaptchaAndCookie();

            if ($this->img !== false) {
                $captcha = new Captcha_ecard($this->img);
                if ($this->login($id, '2025642313', $captcha->result)) {
                    if (!$this->exist($id)) {
                        $this->getPhoto($id);
                        $this->getInfo();
                        //$this->info && $this->insert($id);
                        echo "success: " . $id . "\n";
                    } else {
                        echo "exist: " . $id . "\n";
                    }
                } else {
                    echo "fail: " . $id . "\n";
                }
            } else {
                $id--;
            }
            sleep($interval);
        }
    }

}

//(new Ecard())->run();