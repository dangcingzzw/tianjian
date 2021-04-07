<?php

namespace TC\Cache;

class Redis extends \TC\Cache\Cache
{

    public $host = '127.0.0.1';
    public $port = 6379;
    public $auth = '';
    public $db = 0;
    private $_redis;


    public function getRedis()
    {
        if(!($this->_redis instanceof \Redis)) {

            $this->_redis = new \Redis();
            $connectResult = $this->_redis->connect($this->host,$this->port);
            if(!$connectResult) {
                $this->_redis = null;
                return false;
            }
            if(!empty($this->auth)) {
                $authResult = $this->_redis->auth($this->auth);
                if(!$authResult) {
                    $this->_redis = null;
                    return false;
                }
            }
            $this->_redis->select($this->db);

            $this->_redis->setOption(\Redis::OPT_PREFIX,$this->prefix);
        }

        return $this->_redis;
    }

    public function get($key)
    {
        $values = $this->getRedis()->get($key);
        return $this->_decrypt($values);
    }

    public function set($key,$value,$timeout = null)
    {
        return $this->getRedis()->set($key,$this->_encrypt($value),$timeout);
    }

    public function mget(array $keys)
    {
        $keyValues = $this->getRedis()->mget($keys);
        return array_map(function($value){
            return $this->_decrypt($value);
        },$keyValues);
    }

    public function mset(array $kvs,$timeout=null)
    {
        $kvs = array_map(function($value){
            return $this->_encrypt($value);
        },$kvs);
        return $this->getRedis()->mset($kvs,$timeout);
    }

    public function delete($key)
    {
        return $this->getRedis()->delete($key);
    }
}