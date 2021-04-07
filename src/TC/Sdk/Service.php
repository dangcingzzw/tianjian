<?php
namespace TC\Sdk;
class Service
{
    public $redis;

    public function find($serviceName)
    {
        $service = $this->redis->getRedis()->get($serviceName);
        if(!empty($service)) {
            return json_decode($service,true);
        }
        return '';
    }

    public function register($serviceName,$config)
    {
        return $this->redis->getRedis()->set($serviceName,json_encode($config));
    }
}