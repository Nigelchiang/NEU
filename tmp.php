<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/2
 * Time: 22:06
 */
//$id=20144633;
//var_dump(strval($id));
//$dbc = mysqli_connect('localhost:3306', 'nigel', 'nigel', 'neu') or die("Error connecting to MySQL server.");
//mysqli_select_db($dbc, 'neu');
//$query = "select * from info WHERE id='20147215'";
//$res   = mysqli_query($dbc, $query);
//$array = mysqli_fetch_array($res);
//var_dump($array);

class runTime {

    private $starTime;
    private $stopTime;

    private function getMicTime() {

        $mictime = microtime();
        list($usec, $sec) = explode(' ', $mictime);

        return (float)$usec + (float)$sec;
    }

    public function start() {

        $this->starTime = $this->getMicTime();
    }

    public function stop() {

        $this->stopTime = $this->getMicTime();
    }

    public function spent() {

        return round($this->stopTime - $this->starTime) * 1000;//单位：毫秒数
    }
}

////类使用方法介绍
//$time = new runTime();
//$time->start();//该语句尽量写在代码段的最开始处
//
////程序代码段
//date_default_timezone_set('PRC');
//$a = 100000;
//while ($a--) {
//    date('c');
//    //sleep(1);
//}
//$time->stop();//该语句最好写在代码段的最结尾处
//echo $time->spent();

var_dump(strpos('<!--Add below--><td><table><tr><td nowrap><font color="ffffff" >口令错误.</font>', '口令'));