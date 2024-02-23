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

```