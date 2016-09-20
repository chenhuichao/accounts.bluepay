<?php
class BindUserSvc
{/*{{{*/
	const OBJ = 'bind_user';
	
	static private function add( $param )
	{
		$obj = BindUser::createByBiz( $param );
		return self::getDao()->add( $obj );
	}

	static public function getById( $id = '0' )
	{
		if ( empty( $id ) )
		{
			return null;
		}
		return self::getDao()->getById( $id , self::OBJ );
	}
	
	static public function updateById( $id, $param )
	{
		return self::getDao()->updateById( $id, $param, self::OBJ );
	}

	static private function getDao()
	{
		return LoaderSvc::loadDao( self::OBJ );
	}
	
	static public function lists( $request = array(), $options = array(), $export = false)
	{/*{{{*/
		$request_param = array();
		$sql_condition = array();
		
		if(isset($request['keyword']) && strlen($request['keyword']) > 0)
		{
			$sql_condition[] = "`desc` like '%".$request['keyword']."%' ";
			$options['baseurl'] .= 'keyword='.$request['keyword'];
		}
		
		$option = array();
		$option['len'] = ($options['len'] > 0) ? $options['len'] : PER_PAGE;
		if($options['page'] > 0){
			$option['offset'] = ($options['page'] - 1) * $option['len'];
		}
		$option['orderby'] = isset($options['orderby']) ? $options['orderby'] : '';
		
		$results = self::getDao()->getRecord($sql_condition,$sql_param ,$option);
		
		$pages = '';
		$total = $results['total'];
		if($total > 0)
		{
			$temp = stristr($options['baseurl'],'?');
			if($temp != false && strlen($temp)>1){
				$options['baseurl'] .= '&';
			}
			$pages = Pager::getPageStr($options['page'],$option['len'],$total,$options['baseurl']);
		}
		$results['pages'] = $pages;
		$results['offset'] = $option['offset'] + 1;
		$results['len'] = $option['len'];
		$results['page'] = $option['page'];
		$results['pagenums'] = ceil($total / $option['len']);
		
		return $results;
	}/*}}}*/
	
	private static function getCreateUserLock($key)
	{
		$lock = 'CREATE_USER_'.$key;
		$r = MysqlSvc::getLock($lock);
		return $r;
	}

	private static function releaseCreateUserLock($key)
	{
		$lock = 'CREATE_USER_'.$key;
		$r = MysqlSvc::releaseLock($lock);
		return $r;
	}
	
	public static function createUser($key)
	{
		$uid = self::getUidByKey($key);
		if($uid) return $uid;
		$ret = self::bindUser($key);
		if($ret['e'] == ErrorSvc::ERR_OK){
			return self::getUidByKey($key);
		}
		return false;
	}
	
	public static function bindUser($key)
	{
		$ret = array(
			'e'=>ErrorSvc::ERR_OK,
		);
		$r = self::getCreateUserLock($key);
		if($r){
			LoaderSvc::loadExecutor()->beginTrans();
			
			$uid = self::getUidByKey($key);
			if(!is_null($uid)){
				LoaderSvc::loadExecutor()->rollback();
				self::releaseCreateUserLock($key);
				$ret = array(
					'e'=>ErrorSvc::ERR_BIND_USER_EXIST,
				);
				return $ret;
			}
			
			$params = array(
				'key'=>$key,
			);
			
			$obj = self::add($params);
			if(is_object($obj) && LoaderSvc::loadExecutor()->inTrans()){
				LoaderSvc::loadExecutor()->commit();
				self::releaseCreateUserLock($key);
				return $ret;
			}else{
				LoaderSvc::loadExecutor()->rollback();
				self::releaseCreateUserLock($key);
				$ret = array(
					'e'=>ErrorSvc::ERR_BIND_USER_FAIL,
				);
			}
		}else{
			$ret = array(
				'e'=>ErrorSvc::ERR_MYSQL_GET_LOCK,
			);
		}
		return $ret;
	}

	public static function isDisabled($uid)
	{
		$obj = self::getById($uid);
		if(is_object($obj) && $obj->state == BindUser::STATE_DISABLED) return true;
		return false;
	}
	
	public static function getUidByKey($key)
	{
		$uid = self::getDao()->getUidByKey($key);
		return $uid;
	}


}
