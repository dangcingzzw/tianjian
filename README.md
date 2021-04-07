# 介绍
该包用于yaf,yar为核心的分布式rpc框架核心库
使用该包应确认已安装php相关扩展，如yaf,yar,yac,seaslog,redis

yaf用 C 语言开发的 PHP 框架，相比原生的 PHP，几乎不会带来额外的性能开销
简介
一.IO文件包括输入，输出
输入：将yaf提供的请求类（Yaf_Controller_Abstract::getRequest）与tp验证类结合在一起,封装成\TC\IO\Input类。
输出：将接口响应，错误抛出，返回code码，统一封装\TC\IO\Output类。

优势：参数验证简洁高效，输出健全而统一

二.Db文件
核心为Medoo轻量级ORM,对其进行封装，省略表名，实现单例

三.Client文件
对yar(rpc框架)的封装，并增加trace_id,用于识别请求，管理日志

四.Cache文件
封装redis,yac,并集成wave波式缓存

五.Di文件
基于Yaf_Registry封装的di容器类

六.Sdk文件
封装服务发现和服务注册方法

七.Log文件
封装Seaslog日志类



