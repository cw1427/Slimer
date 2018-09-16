<?php
/**
 * Author: Shawn Chen
 * Desc: The csrf protected route config middleware
 */

return [
    'failure_callable' => function ($request, $response, $next) { //@link https://github.com/slimphp/Slim-Csrf#handling-validation-failure
    $request = $request->withAttribute('csrf_status', false);
    
    return $next($request, $response);
},
];