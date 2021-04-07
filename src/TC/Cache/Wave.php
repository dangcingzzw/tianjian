<?php
namespace TC\Cache;
class Wave
{

    public $dbEmpty= -1;

    public $saveTimes = 3;

    public $localCache;

    public $redisCache;

    public $lockPrefix = 'redis:lock:';
    public function __construct()
    {
        $this->localCache=new \TC\Cache\Yac();
        $this->redisCache=new \TC\Cache\Redis();
    }

    private function _waveDataIsExpire($data,$time)
    {
        return $data['logicExpireAt'] <= $time;
    }

    protected function _waveReturnData($data)
    {
        //数据格式转化
        $data['data'] = ($data['data'] == $this->dbEmpty) ? [] : $data['data'];
        return $data['data'];
    }

    /**
     * @desc 获取二级缓存数据
     * @param $key
     * @param callable $callback
     * @param $timeout
     * @return array|mixed
     * @author zhaozhiwei
     * @date 2021-02-18 8:38
     */
    public function waveGet($key,callable $callback,$timeout)
    {
        $time   = time();
        //优先使用yac缓存
        $yacResult = $this->localCache->get($key);
        //本地读取到
        if(!empty($yacResult)) {
            $isExpire = $this->_waveDataIsExpire($yacResult,$time);

            //数据未过期
            if(!$isExpire) {
                return $this->_waveReturnData($yacResult);
            }
        }

        //yac没有读取到或者已经过期，都需要从redis读取
        $redisResult = $this->redisCache->get($key);

        //redis没有读取到，直接数据库读取
        if(empty($redisResult)) {
            return $this->waveSet($key,$callback,$timeout);
        }

        //redis读取到
        $redisIsExpire = $this->_waveDataIsExpire($redisResult,$time);

        //数据未过期
        if(!$redisIsExpire) {
            //其他端更新了redis缓存
            $this->localCache->set($key,$redisResult,$timeout);

            return $this->_waveReturnData($redisResult);
        }

        //数据无效，获得锁
        $redis = $this->redisCache->getRedis();
        $lockKey = $this->lockPrefix.$key;

        $lockResult = $redis->multi(\Redis::PIPELINE)
            ->incr($lockKey)
            ->expire($lockKey,3)
            ->exec();

        //没有获取到锁,把redis获取结果返回
        if($lockResult[0] > 1) {
            return $this->_waveReturnData($redisResult);
        }

        //获取到锁，刷新缓存
        $data = $this->waveSet($key,$callback,$timeout);

        //释放锁
        $redis->del($lockKey);

        return $data;
    }


    public function waveSet($key,callable $callback,$timeout)
    {
        //数据库读取
        $result = call_user_func($callback);

        //格式化数据
        $data = [
            'data'           => empty($result) ? $this->dbEmpty : $result,
            'logicExpireAt'  => time()+$timeout,
            'timeout'        => $timeout
        ];


        $this->localCache->set($key,$data,$timeout);

        //更新redis缓存
        $this->redisCache->set($key,$data,$this->saveTimes * $timeout);

        return $result;
    }


    public function waveDel($key)
    {
        //本地直接删除，以后从redis直接获取

        $this->localCache->delete($key);

        $result = $this->redisCache->get($key);
        if(empty($result)) {
            return true;
        }

        if(isset($result['logicExpireAt'])) {
            $result['logicExpireAt'] = 0;

            $timeout = isset($result['timeout']) ? (int)$result['timeout'] : 0;

            $this->redisCache->set($key,$result,$this->saveTimes * $timeout);
        }

        return true;
    }
}