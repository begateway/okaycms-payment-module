<?php

namespace Okay\Modules\OkayCMS\BeGateway;

return [
    'BeGateway_callback' => [
        'slug' => 'payment/OkayCMS/BeGateway/callback',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\CallbackController',
            'method' => 'payOrder',
        ],
    ],
];
