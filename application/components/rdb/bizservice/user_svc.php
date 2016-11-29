<?php
class UserSvc
{/*{{{*/
	const OBJ = 'User';
	const PAY_PASSWD_SALT = '9E775232-69ED-4D80-A228-5D72930BE066';
	
	static public function getById($id = '0')
	{
		if (empty($id))
		{
			return null;
		}
		return self::getDao()->getById($id,self::OBJ);
	}

	static public function updateById($id,$param)
	{
		return self::getDao()->updateById($id,$param,self::OBJ);
	}

	static private function getDao()
	{
		return LoaderSvc::loadDao(self::OBJ);
	}

	static public function setPayPasswd($uid,$passwd)
	{
		$hash = self::encodePayPasswd($passwd);
		self::updateById($uid,['pay_passwd'=>$hash]);
		return $hash;
	}

	static public function encodePayPasswd($passwd)
	{
		$hash = hash('sha256',md5($passwd).self::PAY_PASSWD_SALT);
		return $hash;
	}
}