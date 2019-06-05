<?php
return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'aliyun',
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => '/easy-sms.log',
        ],
        'aliyun' => [
            'access_key_id' => 'LTAIB3W9wXEGHzzX',
            'access_key_secret' => 'Nu1BQJ00MwW5w7pN5AlnBMxxTfvJCn',
            'sign_name' => '阿里大于',
        ],
        //...
    ],
];