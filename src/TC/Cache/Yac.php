<?php
namespace TC\Cache;
/**
 * 寄存器缓存类
 * Class Yac
 * @package Cache
 */
class Yac extends \TC\Cache\Cache
{
    private $_cache;

    /**
     * @desc 格式化key,md5加密限制长度
     * @param $key
     * @return string
     * zhaozhiwei
     * 2021/4/6 18:36
     */
    protected function _formatKey($key)
    {
        if(strlen($this->prefix.$key)>48) {
            return md5($key);
        }
        return $key;
    }

    /**
     * @desc 获取yac缓存类
     * @return \Yac
     * zhaozhiwei
     * 2021/4/6 18:37
     */
    public function getCache()
    {
        if(!($this->_cache instanceof \Yac)) {
            $this->_cache = new \Yac($this->prefix);
        }
        return $this->_cache;
    }

    /**
     * @desc 单条数据存储
     * @param $key
     * @param $value
     * @param int $timeout
     * @return mixed
     * zhaozhiwei
     * 2021/4/6 18:37
     */
    public function set($key,$value,$timeout=0)
    {
        $key = $this->_formatKey($key);
        return $this->getCache()->set($key,$value,$timeout);
    }

    /**
     * @desc 数据批量存储
     * @param array $kvs
     * @param int $timeout
     * @return mixed
     * zhaozhiwei
     * 2021/4/6 18:38
     */
    public function mset(array $kvs,$timeout=0)
    {
        $hashKeys = [];
        foreach ($kvs as $key=> $value) {
            $hashKeys[ $this->_formatKey($key) ] = $value;
        }

        return $this->getCache()->set($hashKeys,$timeout);
    }

    /**
     * @desc 获取单条数据
     * @param $key
     * @return mixed
     * zhaozhiwei
     * 2021/4/6 18:38
     */
    public function get($key)
    {
        $key = $this->_formatKey($key);
        return $this->getCache()->get($key);
    }

    /**
     * @desc 获取多条数据
     * @param array $keys
     * @return array
     * zhaozhiwei
     * 2021/4/6 18:39
     */
    public function mget(array $keys)
    {
        $hashKeys = [];
        foreach ($keys as $value) {
            $hashKeys[ $this->_formatKey($value) ] = $value;
        }
        unset($keys);

        $keyValues = $this->getCache()->get(array_keys($hashKeys));

        $data = [];
        foreach ($keyValues as $hashKey => $value) {
            $data[ $hashKeys[ $hashKey ] ] = $value;
        }

        return $data;
    }

    /**
     * @desc 清除缓存
     * @param $keys
     * @return mixed
     * zhaozhiwei
     * 2021/4/6 18:39
     */
    public function delete($keys)
    {
        if(is_array($keys)) {
            $hashKeys = array_map(function($value){
                return $this->_formatKey($value);
            },$keys);
        }else {
            $hashKeys = $this->_formatKey($keys);
        }

        return $this->getCache()->delete($hashKeys);
    }

    /**
     * @desc 清楚所有缓存值
     * @return mixed
     * zhaozhiwei
     * 2021/4/6 18:42
     */
    public function flush()
    {
        return $this->getCache()->flush();
    }

    /**
     * @desc 获取缓存信息
     * @return mixed
     * zhaozhiwei
     * 2021/4/6 18:41
     */
    public function info()
    {
        return $this->getCache()->info();
    }
}