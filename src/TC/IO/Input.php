<?php
namespace TC\IO;

use TC\IO\check\Main;
class Input extends Main{
    /**
     * @desc 参数校验
     * @author zhaozhiwei
     * @date 2021-02-09 9:00
     */
    public function gocheck($scene,$method='get')
    {
        $params=$this->getRequest($method);
        $result = $this->scene($scene)->check($params);
        if ($result !==true) {
            \TC\IO\Output::error($this->getError());
        } else {
            return $params;
        }
    }

    /**
     * zhaozhiwei
     * 2021/4/6 17:35
     */
    protected function getRequest($method){
        $request=\TC\Di\Container::get('Yaf_Com_Dispatcher')->getRequest();
        switch($method){
            case 'get':
                $params =$request->getQuery();
                break;
            case 'post':
                $params =$request->getPost();
                break;
            case 'file':
                $params =$request->getFiles();
                break;
            default:
                $params=$request->getParams();
        }
        return $params;
    }

}