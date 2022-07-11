<?php

return [

    'login' => [
        'users'                     => 'users',
        'children'                  => 'children',
        'children_classes'          => 'children_classes',
        'crm_request'               => 'crm_request',
    ],

    'messenger' => [
        'users'                     => 'cm_users',
        'children'                  => 'cm_student',
        'class'                     => 'cm_class',
        'school'                    => 'cm_schools',
    ],
    'learning' => [
        'packages'                  => 'tbl_learning_packages',
        'package_orders'            => 'tbl_learning_package_orders',
        'subscriptions'             => 'tbl_learning_subscriptions',
        'children'                  => 'tbl_learning_children',
        'crm_request'               => 'tbl_learning_crm_request',
        'users'                     => 'tbl_learning_learning_users',
    ],

];