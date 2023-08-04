<?php

return [
    'CustomerType' => [
        'I' => 'Individual',
        'B' => 'Business'
    ],
    'InvoiceStatus' => [
        'B' => 'Billed',
        'P' => 'Paid',
        'V' => 'Void'
    ],
    'UserAccessType' => [
        'admin' => ['create', 'update', 'delete'],
        'update' => ['create', 'update'],
        'basic' => ['read'],
    ]
];
