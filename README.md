## Installation

Install the latest version with

```bash
$ composer require qdingdong/customer
```

## Basic Usage

```php
<?php

use Qdd\Customer;

// create a client
$client = new QddClient('appkey', 'appSecret');

// 测试环境，并不开启请求日志
// $client = new QddClient('appkey', 'appSecret',true);

// 开启请求日志需设置日志文件
// $client = new QddClient('appkey', 'appSecret',false,true);
// $client->setLogFile('path/to/your.log');


/*
 *---------------------------------------------------------------
 * 同步c端用户信息
 * $clientUserKey 为c端用户唯一标识
 * $avatar         用户头像
 * $nickname       用户昵称
 *---------------------------------------------------------------
 */
$client->addUsers($clientUserKey, $avatar, $nickname);

//响应结构
/*{
  "status":200,
  "content":{
    "user_key":"", //客服侧用户标识
    "nickname":"",
    "avatar":""
  },
  "msg":""
}*/



/*
 *---------------------------------------------------------------
 * 设置c端用户信息
 * $clientUserKey 为c端用户唯一标识
 * $data         用户信息
 *      nickname       用户昵称
 *      avatar         用户头像
 *---------------------------------------------------------------
 * lfq 2024/2/23 16:51
 */
 $client->setUsers($clientUserKey, $data);

//响应结构
/*{
  "status":200,
  "content":{},
  "msg":""
}*/


/*
 *---------------------------------------------------------------
 * 获取平台客服h5 url 地址;用于用户直接跳转到客服聊天页面
 * $userKey 为客服侧用户标识
 * $card     用于客服端展示的卡片信息，可为[]
 *---------------------------------------------------------------
 * lfq 2024/2/23 17:13
 */
 $client->getPlatformUrl($userKey, $card);



/*
 *---------------------------------------------------------------
 * 获取c端用户聊天列表h5 url 地址;用于h5页面，当前用户查看自己的聊天好友
 * $userKey 为客服侧用户标识
 *---------------------------------------------------------------
 * lfq 2024/2/23 17:22
 */
 $client->getChatListUrl($userKey);
 
 
 /*
  *---------------------------------------------------------------
  * 设置正式客服h5 url 地址;可不设置，不设置使用默认；测试环境设置不起作用
  *---------------------------------------------------------------
  * lfq 2024/2/23 17:42
  */
  $client->setCustomerUrl($url);
  
  // 如何未设置，那么 $client->getPlatformUrl($userKey, $card); 会得到如下地址 https://customer-h5.tuoyushipin.com/#/pages/chat/chat?info={}
  // $client->setCustomerUrl('http://www.baidu.com'); 后
  // $client->getPlatformUrl($userKey, $card); 会得到如下地址 http://www.baidu.com/#/pages/chat/chat?info={}
 
 
 
 
 
 
 /*
  *---------------------------------------------------------------
  * 添加商户(支持更新)
  * $username      商户用户名 唯一标识
  * $password      商户密码
  * $merchantName  商户名称 可为'' ,为'' 时取username
  * $logo          商户logo 
  * $maxCustomerNum  最大客服坐席，达到此接入数量后优先转给其他未达到数量客服,max 1000
  * $childUserKey  业务系统用户唯一标识，可为'' ,为'' 时取username
  * $phone         商户电话，可为'' 
  *---------------------------------------------------------------
  * lfq 2024/2/23 17:36
  */
  $client->addMerchant($username, $password, $merchantName, $logo, $maxCustomerNum, $childUserKey, $phone);
  
//响应结构
/*{
  "status":200,
  "content":{
    "merchant_key":"", //客服侧商户标识
    "username":"" //用户名
  },
  "msg":""
}*/

/*
 *---------------------------------------------------------------
 * 设置商户信息
 * $merchantKey 商户唯一标识
 * $data         商户信息
 *      merchant_name       商户名称
 *      logo                商户头像
 *---------------------------------------------------------------
 * lfq 2024/2/23 16:51
 */
 $client->setMerchant($merchantKey, $data);

//响应结构
/*{
  "status":200,
  "content":{},
  "msg":""
}*/



/*
 *---------------------------------------------------------------
 * 获取b端用户聊天列表h5 url 地址;用于h5页面，客服查看自己的聊天用户
 * $merchantKey 为客服侧用户标识
 *---------------------------------------------------------------
 * lfq 2024/2/23 17:22
 */
 $client->getChatWithMerchantListUrl($merchantKey);


```