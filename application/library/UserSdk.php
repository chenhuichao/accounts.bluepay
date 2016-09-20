<?php
include ROOT_PATH . '/src/include/client/user_client.php';
class UserSdk
{
    static private $_instances = array();
    private static $_flag = null;

    static public function getUserInfoByUid($uid)
    {
        $result = self::init()->user_getUserInfoByUid(array('uid'=>$uid));
        return self::unserialize($result);
    }

    static public function getUserInfoBySid($sid)
    {
        $result = self::init()->user_getUserInfoBySid(array('sid'=>$sid));
        return self::unserialize($result);
    }


    public function init($flag = '')
    {/*{{{*/
        $flag = strlen($flag) > 0 ? self::setFlag($flag) : self::getFlag();
        if (!isset($_instances[$flag])) {
            $c = new UserClient($flag);
            $_instances[$flag] = $c;
        }
        return $_instances[$flag];
    }/*}}}*/

    private function unserialize($str)
    {
        $r = unserialize($str);
        if (false === $r) {
            return null;
        }
        return $r;
    }

    // public function auth($username, $passwd, $debug = 1)
    // {
    //     $result = self::init()->user_login(array('mobile'=>$username,'passwd'=>$passwd,'_DEBUG_'=>$debug));
    //     return self::unserialize($result);
    // }
    
    
//  public function logout()
//  {
//      self::init()->user_loginout();
//  }
    
    

    public function setFlag($flag)
    {
        self::$_flag = $flag;
    }

    public function getFlag()
    {
        return self::$_flag;
    }

}/*}}}*/
