<?php

return [
    [
        'name' => 'Accounting',
        'flag' => 'accounting.index',
    ],
    [
        'name' => 'Settings',
        'flag' => 'accountings.settings',
        'parent_flag' => 'accounting.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'accounting.destroy',
        'parent_flag' => 'accounting.index',
    ],
];
