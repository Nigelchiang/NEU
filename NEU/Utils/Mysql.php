<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/6
 * Time: 16:43
 */
namespace Nigel\Utils;

/**
 * 单例模式MySQL 数据库连接
 * Class Mysql
 * @package Nigel\Utils
 */
class Mysql {

    /**
     * @var Mysql 单例本身 必须
     */
    protected static $Mysql;
    /**
     * @var \mysqli
     */
    protected $sql;

    protected function __construct($host, $user, $pass, $db, $charset) {

        $this->sql = new \mysqli($host, $user, $pass, $db);
        $this->sql->set_charset($charset);
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments) {

        if (method_exists($this->sql, $name)) {
            if ($arguments) {
                $this->sql->$name($arguments);
            } else {
                $this->sql->$name();
            }
        }
    }

    /**
     * @param string $host    主机
     * @param string $user    用户名
     * @param string $pass    密码
     * @param string $charset 字符集
     * @param string $db      数据库
     *
     * @return Mysql
     */
    public static function connect($host = 'localhost', $user = 'nigel',
                                   $pass = 'nigel', $db = 'neu', $pass = 'nigel', $charset = 'utf-8') {

        //单例已实例化且没有传入参数
        if (isset(static::$Mysql)) {
            return static::$Mysql;
        }

        //单例没有实例化或者传入了新的参数
        return static::$Mysql = new static($host, $user, $pass, $db, $charset);

    }

    /**
     * @param string $charset
     *
     * @return Mysql
     */
    public function setCharset($charset) {

        $this->sql->set_charset($charset);

        return Mysql::$Mysql;
    }

    /**
     * @param $db
     *
     * @return Mysql
     */
    public function selectDb($db) {

        $this->sql->select_db($db);

        return Mysql::$Mysql;
    }

    /**
     * @param $query
     *
     * @return bool|mixed
     */
    public function getData($query) {

        $result = $this->sql->query($query);

        if (is_bool($result)) {
            return $result;
        }

        $data = $result->fetch_all();
        if (count($data) > 0) {
            return $data;
        }

        return false;
    }

    /**
     * @param $query
     *
     * @return array|bool
     */
    public function getLine($query) {

        $result = $this->sql->query($query);

        if (is_bool($result)) {
            return $result;
        }

        $data = $result->fetch_assoc();

        if (count($data) > 0) {
            return $data;
        }

        return false;
    }

    /**
     * @param $query
     *
     * @return bool|mixed
     */
    public function getVar($query) {

        if ($data = $this->getLine($query)) {
            return reset($data);
        } else {
            return false;
        }
    }

    /**
     * @param $query
     *
     * @return bool|\mysqli_result
     */
    public function runSql($query) {

        return $this->sql->query($query);
    }

    /**
     * @return int
     */
    public function errno() {

        return $this->sql->errno;
    }

    /**
     * @return string
     */
    public function error() {

        return $this->sql->error;
    }

    public function close() {

        $this->sql->$this->sql->close();
    }
}

$query   = "select * from aao WHERE id LIKE '201446%'";
$results = Mysql::connect('neu')->getVar($query);
var_dump(Mysql::connect()->get_client_info());
var_dump(Mysql::connect()->get_server_info());
var_dump($results);