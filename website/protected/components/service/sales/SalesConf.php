<?php
class SalesConf
{
	static function getCustomerSource()
	{
		return array(
			1=>'促销活动',
			2=>'客户推荐',
		);
	}
	
	static function getCustomerGrade()
	{
		return array(
				1=>'A级',
				2=>'B级',
				3=>'C级',
				4=>'D级',
		);
	}
}