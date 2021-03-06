<?php

class ApibaseController extends Yaf_Controller_Abstract
{
    public $uid;
    public $user_table_id = 0;

    const ACCOUNTS_APP_ID = 101;

    static $EXCEPT_CONTROLLER =  ['Notify'];
    /**
     * @brief 接口初始化
     */
    public function init()
    {/*{{{*/
        $c = $this->getRequest()->getControllerName();
        if(in_array($c,self::$EXCEPT_CONTROLLER)){}
        else $this->checkLogin();
        $r = BindUserSvc::isDisabled($this->uid);
        if($r){
        	$ret = $this->initOutPut();
	        $ret['errno'] = '50109';
	        $this->outPut($ret);
        }
    }/*}}}*/

    protected function getAppSessionId()
    {
        return strlen($_SERVER['HTTP_SID']) >= 26 ? $_SERVER['HTTP_SID'] : null;
    }

    public function checkLogin()
    {/*{{{*/
        UserSdk::setFlag(self::ACCOUNTS_APP_ID);
        $sid = $this->getAppSessionId();
        $res = UserSdk::getUserInfoBySid($sid);
        $key = isset($res['merchant_id']) && $res['merchant_id'] > 0 ? $res['merchant_id'] : 0;

        if($key > 0){
            $uid = BindUserSvc::getUidByKey($key);
            if($uid){
                $this->uid = $uid;
                $this->user_table_id = $res['user_id'];
                $this->merchant_table_id = $key;
            }elseif(null == $uid && $key > 0){
                $uid = BindUserSvc::createUser($key);
                if($uid){
                    $this->uid = $uid;
                    $this->user_table_id = $res['user_id'];
                    $this->merchant_table_id = $key;
                }else{
                    $ret = $this->initOutPut();
                    $ret['errno'] = '50000';
                    $this->outPut($ret);
                }
            }
            else{
                $ret = $this->initOutPut();
                $ret['errno'] = '50000';
                $this->outPut($ret);
            }
        }else{
           $ret = $this->initOutPut();
           $ret['errno'] = '50101';
           $this->outPut($ret);
        }
    }/*}}}*/

    public function initOutPut()
    {/*{{{*/
        return Init::output();
    }/*}}}*/
    
    /**
     * 指定格式头信息输出
     */
    public function headJson() 
    {/*{{{*/
        header("HTTP/1.1 200 OK");
        header('Content-type: application/json; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Pragma: no-cache");
    }/*}}}*/

    public function responseJson($errno = 0, $data = [], $msg = '')
    {/*{{{*/
        header('Content-type: application/json');
        $output = array(
            "errno"  => $errno,
            "data" => $data,
            "msg" => $msg,
        );
        echo json_encode($output);
    }/*}}}*/

    public function echoCallback($callback, $json) 
    {/*{{{*/
        header('Content-type: text/html');  
        $callback = htmlspecialchars($callback, ENT_QUOTES);
        echo " $callback($json);";
    }/*}}}*/

    public function outPut($res,$callback = null)
    {/*{{{*/
        if(!empty($res['errno'])) {
            if ($res['msg'] && $res['msg'] != 'succ') {
                $res['msg'] = Error::getMsg($res['errno']).":".$res['msg'];
            } else {
                $res['msg'] = Error::getMsg($res['errno']);
            }
        }

        $json = json_encode($res);
        if($callback) {
            $this->echoCallback($callback, $json);
        }else {
            $this->headJson();
            echo $json;
        }
        exit;
    }/*}}}*/


}/*}}}*/
