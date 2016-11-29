<?php
class UserDao extends BaseDao
{
	const TABLE_NAME = 'user';

	private function getTableName()
	{
		return self::TABLE_NAME;
	}
}
