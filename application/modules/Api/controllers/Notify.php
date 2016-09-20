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

     public function posAction()
     {
        $ret = $this->initOutPut();
        $orderid = 'POS_'.RequestSvc::Request('orderid');
        $merchant_id = RequestSvc::Request('merchant_id');
        $amount = sprintf("%.2f",(RequestSvc::Request('amount',0)));
        $fee = sprintf("%.2f",(RequestSvc::Request('fee',0)));
        $paychannel = PayChannel::CHANNEL_POS_RECHARGE;
        $tradeno = RequestSvc::Request('tradeno');

        $uid = BindUserSvc::getUidByKey($merchant_id);
        if($uid) $this->uid = $uid;
        else{
            $ret = $this->initOutPut();
            $ret['errno'] = '50000';
            $this->outPut($ret);
        }

        if($fee <= 0){
            $ret['errno'] = '50103';
            $this->outPut($ret);
        }
        
        if($amount <= 0){
            $ret['errno'] = '50103';
            $this->outPut($ret);
        }
        if(!in_array($paychannel,PayChannel::$RECHARGE_CHANNEL_OPTIONS)){
            $ret['errno'] = '50105';
            $this->outPut($ret);
        }
            

        $result = TransactionSvc::getByOrderid($orderid);
        if(!empty($result)){
           $transid = $result['id'];
           $_amount_  = $result['tin'];
           $_fee_ = $result['fee'];
        }else{
            $params = array(
                'orderid'=>$orderid,
                'btype'=>Transaction::BTYPE_RECHARGE,
                'uid'=>$this->uid,
                'type'=>Transaction::TYPE_IN,
                'amount'=>$amount,
                'fee'=>$fee,
            );
            $transid = TransactionSvc::addTrans($params);
            $_amount_ = $amount;
            $_fee_ = $fee;
        }
       

        $cat = Accountingrecord::CAT_RECHARGE;
        $from = Accountingrecord::FROM_POS;
        $remark = 'POS Recharge';
        
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
