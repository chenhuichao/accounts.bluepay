<?php
function auto_load($classname)
{/*{{{*/
		$entity_ucfirst = "";
        $classpath = array(
		"BindUserDao" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/dao/BindUser_dao.php",
		"AccountingrecordDao" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/dao/accountingrecord_dao.php",
		"AccountsDao" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/dao/accounts_dao.php",
		"BaseDao" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/dao/base_dao.php",
		"FreezesDao" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/dao/freezes_dao.php",
		"SysinfoDao" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/dao/sysinfo_dao.php",
		"TransactionDao" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/dao/transaction_dao.php",
		"BindUser" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/entity/BindUser.php",
		"Accountingrecord" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/entity/accountingrecord.php",
		"Accounts" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/entity/accounts.php",
		"Freezes" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/entity/freezes.php",
		"Sysinfo" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/entity/sysinfo.php",
		"Transaction" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/entity/transaction.php",
		"PayChannel" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/knowledge/PayChannel.php",
		"AlipayHelper" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/knowledge/pay/AlipayHelper.php",
		"ChannelAlipayMobile" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/knowledge/pay/ChannelAlipayMobile.php",
		"ChannelBalancePay" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/knowledge/pay/ChannelBalancePay.php",
		"ChannelBasePay" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/knowledge/pay/ChannelBasePay.php",
		"AlipayConfig" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/knowledge/pay/alipay/alipay.config.php",
		"AlipayNotify" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizdomain/knowledge/pay/alipay/lib/alipay_notify.class.php",
		"BindUserSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/BindUser_svc.php",
		"AccountingrecordSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/accountingrecord_svc.php",
		"AccountsSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/accounts_svc.php",
		"ErrorSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/error_svc.php",
		"FreezesSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/freezes_svc.php",
		"LoaderSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/loader_svc.php",
		"LogSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/log_svc.php",
		"MysqlSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/mysql_svc.php",
		"RequestSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/request_svc.php",
		"RequestfilterSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/requestfilter_svc.php",
		"SnSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/sn_svc.php",
		"SysinfoSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/sysinfo_svc.php",
		"TransactionSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/transaction_svc.php",
		"UtlsSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/bizservice/utls_svc.php",
		"Captcha" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/captcha.php",
		"DBCache" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/dbcache.php",
		"Entity" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/entity.php",
		"IDGenter" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/id_genter.php",
		"LogObject" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/log_object.php",
		"ObjectFinder" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/object_finder.php",
		"Pager" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/pager.php",
		"MysqliSessDriver" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/session.php",
		"SessionSvc" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/session.php",
		"SimpleDB" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/simple_db.php",
		"SimpleObject" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/simple_object.php",
		"SQLExecutor" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/sql_executor.php",
		"Timer" => "/home/liuweidong/project/accounts.bluepay/application/components/rdb/integration/timer.php",
		);
        if (isset($classpath[$classname]))
        {
            include($classpath[$classname]);
        }
}/*}}}*/
spl_autoload_register('auto_load');
