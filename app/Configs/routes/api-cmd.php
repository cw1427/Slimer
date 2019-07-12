<?php
/**
 * Author: Shawn Chen
 * Desc: 
 */

return [
    'dbinit' => [
        'pattern' => '/dbinit',
        'methods' => ['GET','POST'],
        'namespace' => '\\App\\Api\\',
	    'perm' => [
            'root'
        ]
    ],
];