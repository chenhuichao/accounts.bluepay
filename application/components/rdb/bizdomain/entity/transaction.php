<?php
class Transaction extends Entity
{
	const STATE_INIT = 99;
	const STATE_SUCC = 1;
	const STATE_PROCESSING = 2;
	const STATE_FAIL = 3;

	static $STATE_OPTIONS = array(
		self::STATE_INIT,
		self::STATE_PROCESSING,
		self::STATE_SUCC,
		self::STATE_FAIL,
	);

	static $STATE_CONF = array(
		self::STATE_INIT =>array('NAME'=>'transaction.entity.state.init'),
		self::STATE_SUCC =>array('NAME'=>'transaction.entity.state.succ'),
		self::STATE_PROCESSING =>array('NAME'=>'transaction.entity.state.processing'),
		self::STATE_FAIL =>array('NAME'=>'transaction.entity.state.fail'),
	);
	
    const BTYPE_UNKNOWN     = 10;
	const BTYPE_REFUND      = 11;
	const BTYPE_CASH		= 12;
	const BTYPE_RECHARGE    = 15;

	

	static $BTYPE_CONF = array(
		self::BTYPE_UNKNOWN =>array('NAME'=>'transaction.entity.btype.unknown'),
		self::BTYPE_REFUND =>array('NAME'=>'transaction.entity.btype.refund'),
		self::BTYPE_CASH =>array('NAME'=>'transaction.entity.btype.cash'),
		self::BTYPE_RECHARGE =>array('NAME'=>'transaction.entity.btype.recharge'),	
	);

	static $BTYPE_OPTIONS = array(
		self::BTYPE_REFUND,
		self::BTYPE_CASH,
		self::BTYPE_RECHARGE,
	);

	const TYPE_IN  	 	  = 1;
	const TYPE_OUT  	  = -1;

	static $TYPE_OPTIONS = array(
		self::TYPE_IN,
		self::TYPE_OUT,
	);

	static $TYPE_CONF = array(
		self::TYPE_IN =>array('NAME'=>'transaction.entity.type.in'),
		self::TYPE_OUT =>array('NAME'=>'transaction.entity.type.out'),
	);


	const SSTATE_UNSETTLED = 100;
	const SSTATE_SETTLED = 101;

	static $SSTATE_OPTIONS = array(
		self::SSTATE_UNSETTLED,
		self::SSTATE_SETTLED,
	);
	
	static $SSTATE_CONF = array(
		self::SSTATE_UNSETTLED => array('NAME'=>'transaction.entity.sstate.unsettled'),
		self::SSTATE_SETTLED => array('NAME'=>'transaction.entity.sstate.settled'),
	);

	const ID_OBJ  = 'transaction';

	public static function createByBiz( $param )
	{
		$cls = __CLASS__;
		$obj = new $cls();
		$obj->id = LoaderSvc::loadIdGenter()->create(self::ID_OBJ);
		$obj->ctime = date('Y-m-d H:i:s');
		$obj->utime = date('Y-m-d H:i:s');
		$obj->orderid = strlen($param['orderid']) > 0 ? $param['orderid'] : 'H'.$obj->id;
		$obj->tin = is_null($param['tin']) ? 0 : (double)$param['tin'];
		$obj->tout = is_null($param['tout']) ? 0 : (double)$param['tout'];
		$obj->fee = is_null($param['fee']) ? 0 : (double)$param['fee'];
		$obj->type = in_array($param['type'],self::$TYPE_OPTIONS) ? $param['type'] : self::TYPE_IN;
		$obj->datetime = !isset($param['datetime']) ? date('Y-m-d H:i:s') : $param['datetime'];
		$obj->uid = is_null($param['uid']) ? -1 : $param['uid'];
		$obj->remark = is_null($param['remark']) ? '' : $param['remark'];
		$obj->btype = in_array($param['btype'],self::$BTYPE_OPTIONS) ? $param['btype'] : self::BTYPE_UNKNOWN;
		$obj->state = in_array($param['state'],self::$STATE_OPTIONS) ? $param['state'] : self::STATE_INIT;
		$obj->sstate = in_array($param['sstate'],self::$SSTATE_OPTIONS) ? $param['sstate'] : self::SSTATE_UNSETTLED;
		$obj->channelid = is_null($param['channelid']) ? PayChannel::CHANNEL_UNKNOWN : $param['channelid'];
		$obj->tradeno = is_null($param['tradeno']) ? '' : $param['tradeno'];
		$obj->user_id = is_null($param['user_id']) ? 0 : $param['user_id'];
		$obj->merchant_id = is_null($param['merchant_id']) ? 0 : $param['merchant_id'];
		$obj->sn = is_null($param['sn']) ? '' : $param['sn'];
		return $obj;
	}
}
