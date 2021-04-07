<?php

namespace TC\Cache;
abstract class Cache
{
    public $prefix = 'cache2:';

    /**
     * @desc 数据加密
     * @param $data
     * @return false|string
     * zhaozhiwei
     * 2021/4/6 18:33
     */
    protected function _encrypt($data)
    {
        return json_encode(['data' => $data]);
    }

    /**
     * @desc 数据解密
     * @param $data
     * @return false|mixed
     * zhaozhiwei
     * 2021/4/6 18:34
     */
    protected function _decrypt($data)
    {
        if (empty($data)) {
            return false;
        }

        return json_decode($data, true)['data'];
    }

    abstract public function get($key);

    abstract public function set($key, $value, $timeout = 0);

    abstract public function mget(array $keys);

    abstract public function mset(array $kvs, $timeout = 0);

    abstract public function delete($key);
}