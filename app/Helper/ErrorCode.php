<?php

namespace Helper;

class ErrorCode
{
    const ERROR_NO_DBMS = 1;
    const ERRPR_FRAMEWORK_NO_SUPPORT_DRIVER=2;
    const ERROR_WRONG_PARMS = 1400;
	
    public static $ErrorCommon = "Response";

    public static $ERROR_MESSAGE = array(
       	self::ERROR_NO_DBMS=>"The Sql no DBMS error",
       	self::ERRPR_FRAMEWORK_NO_SUPPORT_DRIVER=>"%s is not support driver",
       	self::ERROR_WRONG_PARMS=>"Wrong parms",
    );

    public static function getMessage($msgid)
    {
        if( isset(self::$ERROR_MESSAGE[$msgid]) ) {
            return self::$ERROR_MESSAGE[$msgid];
        }
        return FALSE;
    }

    public static function throwErrorMessage(\Exception $except)
    {
        return array(
            'StatusCode' => $except->getCode(),
            'Message' => $except->getMessage()
        );
    }

}