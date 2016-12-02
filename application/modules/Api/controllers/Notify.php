<?php

/**
 * @brief 异步通知处理
 */
class NotifyController extends ApibaseController
{
    /** 
    * @brief 充值成功回调
    * 
    * @author liuweidong
    * @date 2015-11-14
    * @return 
    */ 
    public function alipayAction()
    {/*{{{*/  	
    	$channelAlipayMobileObj = new ChannelAlipayMobile;
    	$ret = $channelAlipayMobileObj->processNotify ($_GET,$_POST,'php://input');
    	
    	if($ret['e'] != ErrorSvc::ERR_OK){
    		AlipayHelper::responseFail();
    	}
    	
    	if($ret['data']['state'] == AlipayHelper::TRADE_FINISHED){
    		AlipayHelper::responseSucc();
    	}
    	
    	$transid = $ret['data']['transid'];
    	$tradeno = $ret['data']['tradeno'];
		$amount = $ret['data']['amount'];
         
        $obj = TransactionSvc::getById($transid);
        
        
        if(!is_object($obj)) {
        	LogSvc::fileLog('Notify_'.__CLASS__.'.'.__FUNCTION__,"交易号[{$transid}]不存在");
        	AlipayHelper::responseFail();
        }
        if($obj->state == Transaction::STATE_SUCC){
        	AlipayHelper::responseSucc();
        }
        
        $_amount_ = $obj->tout > 0 ? $obj->tout : $obj->tin;
        if($_amount_ != $amount){
        	LogSvc::fileLog('ERROR_'.__CLASS__.'.'.__FUNCTION__,"[transid:{$transid}]--[amount:{$amount}]--[msg:金额不匹配]");
        	AlipayHelper::responseFail();
        }
        
        $uid = $obj->uid;
        $accountinfo = AccountsSvc::getByUidAndCat($uid);
        if(empty($accountinfo)){
        	LogSvc::fileLog('ERROR_'.__CLASS__.'.'.__FUNCTION__,"[uid:{$uid}]--[msg:账户不存在]");
        	AlipayHelper::responseFail();
        }
        $accountid = $accountinfo['id'];
        
        if($obj->btype == Transaction::BTYPE_RECHARGE){
        	$cat = Accountingrecord::CAT_RECHARGE;
	        if($ret['data']['state'] == AlipayHelper::TRADE_SUCCESS){
	        	$from = Accountingrecord::FROM_ALIPAY;
	        	$remark = '测试';
	        	
	        	$params = array(
	        		'tradeno'=>$tradeno,
	        		'channelid'=>PayChannel::CHANNEL_ALIPAY_MOBILE,
	        		'amount'=>$_amount_,
	        		'remark'=>$remark,
	        	);
	        	$ret = AccountsSvc::accountingProcess($params,$accountid,$transid,$cat,$from,$remark);
	        	if($ret['e'] == ErrorSvc::ERR_OK){
	        		AlipayHelper::responseSucc();
	        	}
	        }
        }
        //支付操作
        else{
        	//更新交易记录
        	$params = array(
        		'state'=>Transaction::STATE_SUCC,
        		'tradeno'=>$tradeno,
        		'channelid'=>PayChannel::CHANNEL_ALIPAY_MOBILE,
        	);
        	$r = TransactionSvc::updateById($transid,$params);
        	AlipayHelper::responseSucc();
        }
        AlipayHelper::responseFail();
    }/*}}}*/

    private function authAccess()
    {
        $_Allow_Access_IP = array(
            '127.0.0.1',
            '120.76.225.218',
            '221.223.80.158',
        );

        $_Allow_Access_App = array(
            '101','201',
        );

        $tmparr = explode('|',$_SERVER['HTTP_AUTHORIZATION']);
        $_App_Id = isset($_REQUEST['appid']) ? $_REQUEST['appid'] : $tmparr[1];
        $_Client_IP = UtlsSvc::getClientIP();
        if(in_array($_App_Id,$_Allow_Access_App) /*&& in_array($_Client_IP,$_Allow_Access_IP)*/){
            return true;
        }
        return false;
    }

    public function posPreApplyAction()
    {
        $ret = $this->initOutPut();
        $orderid = 'POS'.RequestSvc::Request('orderid');
        $merchant_id = RequestSvc::Request('merchant_id');
        $amount = sprintf("%.2f",(RequestSvc::Request('amount',0)));
        $fee = sprintf("%.2f",(RequestSvc::Request('fee',0)));
        $sn = RequestSvc::Request('sn');
        $user_id = RequestSvc::Request('user_id',0);

        if(!$this->authAccess()){
            $ret['errno'] = '50111';
            $this->outPut($ret);
        }

        $uid = BindUserSvc::getUidByKey($merchant_id);
        if($uid) $this->uid = $uid;
        else{
            $ret['errno'] = '50000';
            $this->outPut($ret);
        }

        if($fee < 0){
            $ret['errno'] = '50103';
            $this->outPut($ret);
        }
        
        if($amount <= 0){
            $ret['errno'] = '50103';
            $this->outPut($ret);
        }
        $params = array(
            'orderid'=>$orderid,
            'btype'=>Transaction::BTYPE_RECHARGE,
            'uid'=>$this->uid,
            'type'=>Transaction::TYPE_IN,
            'amount'=>$amount,
            'fee'=>$fee,
            'user_id'=>$user_id,
            'sn'=>$sn,
            'merchant_id'=>$merchant_id,
        );
        $transid = TransactionSvc::addTrans($params);
        $ret['data'] = ['transid'=>$transid];
        $this->outPut($ret);
    }

    public function posCallbackAction()
    {
        $ret = $this->initOutPut();
        $transid = RequestSvc::Request('transid');
        $merchant_id = RequestSvc::Request('merchant_id');
        $remark = RequestSvc::Request('remark');
        
        $paychannel = RequestSvc::Request('paychannel') > 0 ? RequestSvc::Request('paychannel') : PayChannel::CHANNEL_POS_RECHARGE;
        $tradeno = RequestSvc::Request('tradeno');
        $state = RequestSvc::Request('state');

        if(!$this->authAccess()){
            $ret['errno'] = '50111';
            $this->outPut($ret);
        }

        $result = TransactionSvc::getById($transid);
        if(!empty($result) && $result->state == Transaction::STATE_INIT){
           $transid = $result->id;
           $_amount_  = $result->tin;
           $_fee_ = $result->fee;
        }else{
            $ret['errno'] = '50108';
            $this->outPut($ret);
        }

        if($state == Transaction::STATE_SUCC) goto T_SUCC;
        else goto T_FAIL;

        T_FAIL:
            $remark = 'POS Recharge Fail| '.$remark;
            TransactionSvc::updateById($transid,array('state'=>Transaction::STATE_FAIL,'remark'=>$remark));
            $this->outPut($ret);

        T_SUCC:
        if(!in_array($paychannel,PayChannel::$RECHARGE_CHANNEL_OPTIONS)){
            $ret['errno'] = '50105';
            $this->outPut($ret);
        }
        
        $cat = Accountingrecord::CAT_RECHARGE;
        $from = Accountingrecord::FROM_POS;
        $remark = 'POS Recharge Success | '.$remark;
        
        $uid = BindUserSvc::getUidByKey($merchant_id);
        if($uid) $this->uid = $uid;
        else{
            $ret = $this->initOutPut();
            $ret['errno'] = '50000';
            $this->outPut($ret);
        }
        $accountinfo = AccountsSvc::getByUidAndCat($uid);
        $accountid = $accountinfo['id'];
        $params = array(
            'tradeno'=>$tradeno,
            'channelid'=>$paychannel,
            'amount'=>$_amount_,
            'remark'=>$remark,
            'fee'=>$_fee_,
        );
        $res = AccountsSvc::accountingProcess($params,$accountid,$transid,$cat,$from,$remark);
        if($res['e'] == ErrorSvc::ERR_OK || $res['e'] == ErrorSvc::ERR_TRANSACTION_RESPONSE_REPEAT){
            $ret['errno'] = '0';
            $this->outPut($ret);
        }

        $ret['errno'] = '50108';
        $this->outPut($ret);
    }


}
