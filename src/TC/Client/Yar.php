<?php

namespace TC\Client;
class Yar
{
    public $address;
    private $_client;
    public function getClient()
    {
        if($this->_client instanceof \Yar_Client) {
            return $this->_client;
        }
        $this->_client = (new \Yar_Client($this->address))->SetOpt(YAR_OPT_TIMEOUT, 0);

        return $this->_client;
    }


    public function call($method,array $params=[])
    {
        $params['traceId'] = uniqid('yafr_');

        return $this->getClient()->call($method,[ $params ]);
    }
}