<?php

namespace Qdd\Customer;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class QddClient
{
    //应用key
    private $appKey = '';
    //应用密钥
    private $appSecret = '';

    //请求终端类型
    private $source = 10;
    //接口版本
    private $version = 'v1';

    //正式接口地址
    private $url = 'https://customer-api.tuoyushipin.com/';
    //测试接口地址
    private $testUrl = 'http://test-customer-api.tuoyushipin.com/';

    //开发模式
    private $isTest = false;
    //是否记录请求日志
    private $isLog = false;
    /**
     * @var mixed
     */
    private $logFile;

    public function __construct($appKey, $appSecret, $isTest = false, $isLog = false)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->isTest = $isTest;
        $this->isLog = $isLog;
    }

    public function setLogFile($file)
    {
        $this->logFile = $file;
    }

    //添加c端用户
    public function addUsers($clientUserKey, $avatar, $nickname)
    {
        $params = [
            'avatar' => $avatar,
            'client_user_key' => $clientUserKey,
            'nickname' => $nickname
        ];
        return $this->request('Sync/user', $params);
    }

    //获取请求url
    private function getUrl()
    {
        return $this->isTest ? $this->testUrl : $this->url;
    }

    /**
     * 加密签名
     * @param $data
     * @return string
     */
    private function getSign($data, $buff = "", $oneMd5 = false)
    {
        ksort($data);
        $sign = $this->toUrlParams($data, $buff);
        if ($oneMd5) return md5($sign);
        return md5(md5($sign));
    }

    /**
     * 拼接签名字符串
     * @param array $urlObj
     * @return string 返回已经拼接好的字符串
     */
    private function toUrlParams($urlObj, $buff = "")
    {
        foreach ($urlObj as $k => $v) {
            if ($k == "sign") continue;
            if (is_array($v)) continue;
            if ($v === true) $v = "true";
            if ($v === false) $v = "false";
            $buff .= $k . "=" . htmlspecialchars_decode($v) . "&";
        }
        return trim($buff, "&");
    }

    private function request($method, $params)
    {
        $url = $this->getUrl() . '/other';
        $text = 'url:' . $url . "|";
        $params['method'] = $method;
        $params['time'] = time();
        $params['version'] = $this->version;
        $params['app_key'] = $this->appKey;
        $params['source'] = $this->source;
        $params['sign'] = $this->getSign($params, $this->appSecret);
        $text .= 'params:' . json_encode($params, JSON_UNESCAPED_UNICODE) . "|";
        $response = (new Client())->post($url, $params);
        $response = $response->getBody()->getContents();
        $text .= 'result:' . $response;
        if ($this->isLog && !empty($this->logFile)) {
            $log = new Logger('name');
            $log->pushHandler(new StreamHandler($this->logFile));
            $log->info($text);
        }
        return $response;
    }
}