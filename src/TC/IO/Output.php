<?php
namespace TC\IO;
class Output
{
    /**
     * @desc rpc成功输出
     * @param array $data
     * @param $http_code
     * @param $code
     * @param $msg
     * @return array
     * zhaozhiwei
     * 2021/4/6 17:57
     */
    public static function rpcSuccess($data = [], $http_code=200,$code=200,$msg='success')
    {
        return [
            'http_code' => $http_code,
            'code' => $code,
            'msg' => $msg,
            'rpc_status'=>1,
            'data' => $data
        ];
    }

    /**
     * @desc rpc失败服务
     * @param $http_code
     * @param $code
     * @param $msg
     * @param array $data
     * @return array
     * zhaozhiwei
     * 2021/4/6 17:58
     */
    public static function rpcFail($http_code, $code,$msg,$data=[])
    {
        return [
            'http_code' => $http_code,
            'code' => $code,
            'msg' => $msg,
            'rpc_status'=>-1,
            'data' => $data
        ];

    }

    /**
     * @desc 结束程序错误输出
     * @param string $msg
     * zhaozhiwei
     * 2021/4/6 17:59
     */
    public static function OverError($msg = '')
    {
        echo json_encode([
            'code' => 4004,
            'msg' => $msg,
            'data' => [],
            'http_code' => 400,],
            JSON_UNESCAPED_UNICODE);;
        exit;
    }

    /**
     * Rest返回校验
     * @param $res
     * zhaozhiwei
     * 2021/4/6 18:02
     */
    public static function OverSuccess($res){
        if(isset($res['rpc_status']) && $res['rpc_status']>0){
            exit(json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        exit(json_encode(['msg'=>'网络错误','code'=>9909,'http_code'=>500,'data'=>[]],JSON_UNESCAPED_UNICODE));
    }
}