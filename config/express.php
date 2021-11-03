<?php

return [
    // 服務位置
    'express_url' => env('EXPRESS_URL', 'https://logistics-stage.ecpay.com.tw/Express/v2/'),
    // 特店編號
    'merchant_id' => env('EXPRESS_MERCHANT_ID', '2000132'),
    // HashKey
    'hash_key' => env('EXPRESS_HASH_KEY', '5294y06JbISpM5x9'),
    // HashIV
    'hash_iv' => env('EXPRESS_HASH_IV', 'v77hoKGq4kWxNNIS'),
    // 串接規格文件版號
    'vision' => env('EXPRESS_VISION', '1.0.0'),
];
