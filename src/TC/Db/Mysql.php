<?php
namespace TC\Db;
abstract class Mysql
{
    const Table_Name = ''; //表名
    const Database_Name = ''; //数据库名称
    const Database_Type = '';
    const Server = '';
    const User_Name = '';

    //数据库配置
    private static $dbConf = [];

    //数据库连接信息
    private static $dbConnect = [];

    //数据库查询等的返回结果
    private static $sqlRes = [];


    protected static $writeFuncitonList = [
        "insert" => true,
        "update" => true,
        "delete" => true,
        "replace" => true,
        "id" => true,
        "select_master" => true,
        "get_master" => true,
        "has_master" => true,
        "count_master" => true,
        "max_master" => true,
        "min_master" => true,
        "avg_master" => true,
        "sum_master" => true
    ];

    protected static $readFunctionList = [
        "select" => true,
        "get" => true,
        "has" => true,
        "count" => true,
        "max" => true,
        "min" => true,
        "avg" => true,
        "sum" => true
    ];

    protected static $transactionFunctionList = [
        "action" => true
    ];

    protected static $debugFunctionList = [
        "debug" => true,
        "error" => true,
        "log" => true,
        "last" => true,
        "query" => true
    ];

    public static function __callStatic($method, $arguments)
    {

        if (isset(self::$readFunctionList[$method]) || isset(self::$writeFuncitonList[$method])) {
            self::$dbConnect['mysql'] = self::getConnection();
            array_unshift($arguments, static::Table_Name);

        } else if(isset(self::$debugFunctionList[$method]) || isset(self::$transactionFunctionList[$method])) {
            self::$dbConnect['mysql'] = self::getConnection();

        } else {
            exit("use undefined Medoo function:" . $method . "-" . json_encode($arguments));
        }
        self::$sqlRes['res'] = call_user_func_array([self::$dbConnect['mysql'], $method], $arguments);

        return  self::$sqlRes['res'];
    }

    public static function getConnection()
    {

        if (!isset(self::$dbConnect['mysql'])) {
            self::$dbConnect['mysql'] = new \TC\Db\medoo\Medoo(self::getDbConf());
        }
        return self::$dbConnect['mysql'];
    }

    private function __clone()
    {
        return null;
    }

    public static function getDbConf()
    {
        $DbConf = \Yaf\Registry::get('db_config');
         self::$dbConf['mysql'] = [
             'database_type' => static::Database_Type ? static::Database_Type : $DbConf['type'],
             'database_name' => static::Database_Name?static::Database_Name:$DbConf['name'] ,
             'server' => $DbConf['server'],
             'username' => static::User_Name ? static::User_Name : $DbConf['username'],
             'password' => $DbConf['password'],
             'charset' => $DbConf['charset'],
             'port' => $DbConf['port'],
             'option' => [
                 \PDO::ATTR_CASE => \PDO::CASE_NATURAL
             ]
         ];
        return self::$dbConf['mysql'];
    }
}
