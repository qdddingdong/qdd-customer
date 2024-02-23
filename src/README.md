## Installation

Install the latest version with

```bash
$ composer require qdd/customer
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



//






```