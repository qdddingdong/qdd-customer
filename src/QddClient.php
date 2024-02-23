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
    //正式客服h5地址
    private $customerUrl = 'https://customer-h5.tuoyushipin.com/';
    //测试客服h5地址
    private $testCustomerUrl = 'http://test-customer-h5.tuoyushipin.com';

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

    //设置c端用户资料
    public function setUsers($clientUserKey, $data = [])
    {
        $params = [
            'user_key' => $clientUserKey
        ];
        if (!empty($data['nickname'])) {
            $params['nickname'] = $data['nickname'];
        }
        if (!empty($data['avatar'])) {
            $params['avatar'] = $data['avatar'];
        }
        return $this->request('Sync/setUserAttr', $params);
    }

    //获取平台客服h5 url 地址
    public function getPlatformUrl($userKey, $card = [])
    {
        $params = [
            'app_key' => $this->appKey,//应用分配的key
            'user_key' => $userKey,//当前用户 客服系统分配的唯一标识
            'client_user_key' => '',//当前用户  业务端的唯一标识号
            'nickname' => '',//当前用户业务端昵称
            'avatar' => '',//当前用户头像
            'merchant_key' => '',// 客服系统店铺的唯一标识 (C和B聊天时使用)
            'receive_user_key' => '',//接收的C端用户ID (C和C聊天使用)
            'client_merchant_key' => '',//接收的业务端店铺唯一标识 (C和B聊天使用)
            'org_id' => '',//平台客服的分组ID (C和平台聊天时使用)
            'card' => $card ? json_encode($card, 256) : ''
        ];
        $paramsJson = json_encode($params, 256);
        return $this->getCustomerUrl() . "/#/pages/chat/chat?info=" . $paramsJson;
    }

    //设置正式客服h5 url 地址
    public function setCustomerUrl($url)
    {
        $this->customerUrl = $url;
    }

    //获取c端用户聊天列表h5 url 地址
    public function getChatListUrl($userKey, $source = 10)
    {
        $source = $source ?: $this->source;
        $time = time();
        $aes = $this->base64Encode(openssl_encrypt(json_encode(['time' => $time, 'user_key' => $userKey]), 'AES-128-ECB', 'zAfR%4hrIaF8!ykY', 0));
        return $this->getUrl() . "/api/Customer/usersCustomListUrl?code={$aes}&source={$source}&time=" . $time;
    }






    //添加商户
    public function addMerchant($username, $password, $merchantName, $logo, $maxCustomerNum = 0, $childUserKey = '', $phone = '')
    {
        $params = [
            'username' => $username,
            'max_customer_num' => $maxCustomerNum,
            'pwd' => $password,
            'nickname' => $merchantName ?: $username,
            'merchant_name' => $merchantName,
            'phone' => $phone,
            'child_user_key' => $childUserKey ?: $username,
            'logo' => $logo
        ];
        return $this->request('Sync/merchant', $params);
    }





    //获取请求url
    private function getUrl()
    {
        return $this->isTest ? $this->testUrl : $this->url;
    }

    //获取客服h5地址
    private function getCustomerUrl()
    {
        return $this->isTest ? $this->testCustomerUrl : $this->customerUrl;
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

    private function base64Encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/'), array('-', '_', ''), $data);
        return $data;

    }

    private function base64Decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('===', $mod4);
        }
        return base64_decode($data);
    }
}