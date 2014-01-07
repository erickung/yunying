<?php
class ArrayLogServAdapter extends LogServAdapter
{
	protected function getName($log)
	{
		return 'array_' . self::$log_num;
	}
	
	protected function getLog(array $log)
	{
		return $log;
	}
}