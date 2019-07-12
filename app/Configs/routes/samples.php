<?php

return [
    'form' => [
        'pattern' => '/form',
        'methods' => ['GET','POST'],
    ],
    'form_submit' => [
        'pattern' => '/formsubmit',
        'methods' => ['POST'],
    ],
    'table' => [
        'pattern' => '/table',
        'methods' => ['GET','POST'],
    ],
    'table_list' => [
        'pattern' => '/tablelist',
        'methods' => ['GET','POST'],
    ],
    'table_add' => [
        'pattern' => '/tableadd',
        'methods' => ['GET','POST'],
    ],
    'table_edit' => [
        'pattern' => '/tableedit',
        'methods' => ['GET','POST'],
    ],
    'table_delete' => [
        'pattern' => '/tabledelete',
        'methods' => ['DELETE'],
    ],

];