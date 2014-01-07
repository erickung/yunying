<?php
class OperateLogServ
{
	const BEFORE_COMMIT = 'before';
	const AFTER_COMMIT = 'after';
	const ADD_COMMIT = 'add';
	
	private static $ins;
	private static $logs = array();
	private static $log_num = 0;
	private static $ar_labels = array();
	private static $original_data = array();
	
	public static function Instance($active_record = null)
	{
		if (self::$ins !== null)
			return self::$ins;
	
		//Yii::import('application.components.service.active_record.*');
		return self::$ins = new self();
	}
	
	public static function addLogBeforeCommit(ActiveRecordInterface $ar)
	{
		self::$log_num++;
		
		$name = get_class($ar);
		if (!isset(self::$logs[$name])) 
			self::$logs[$name] = array(
					self::BEFORE_COMMIT => array(),
					self::AFTER_COMMIT => array(),
					self::ADD_COMMIT => array(),
			);

		self::$logs[$name][self::BEFORE_COMMIT][self::$log_num] = $ar->getAttributes();
		self::$ar_labels[$name] = $ar->attributeLabels();
	}
	
	public function addLogAfterCommit(CMSActiveRecord $ar, $add = false)
	{		
		$name = get_class($ar);
		self::$logs[$name][self::AFTER_COMMIT][self::$log_num] = $ar->getAttributes();
		if ($add)
		{
			self::$logs[$name][self::ADD_COMMIT][self::$log_num] = $ar->getAttributes();
			if (!isset(self::$ar_labels[$name])) self::$ar_labels[$name] = $ar->attributeLabels();
			
		}
		else
		{
			self::$logs[$name][self::AFTER_COMMIT][self::$log_num] = $ar->getAttributes();
		}

	}
	
	public static function log($module_id)
	{
		return ;
		$log = new OperateLogAR();
		$log->operation_record = self::getLogData();
		if (!$log->operation_record) return ;
		$log->opt_username = WebUser::Instance()->user->user_name;
		$log->module_id = $module_id; 
		$log->create_time = new CDbExpression('NOW()');
		$log->add();
	}
	
	private static function getLogData()
	{
		$logs = array();//print_r(self::$logs);
		foreach (self::$logs as $ar_name => $log_data)
		{
			$logs[$ar_name] = self::getARLogData($log_data);
		}
		
		return self::getLogtext($logs);
	}
	
	private static function getARLogData($log_data)
	{
		$logs = array(
			self::BEFORE_COMMIT => array(),
			self::AFTER_COMMIT => array(),	
			self::ADD_COMMIT => array(),	
		);
		
		if (isset($log_data[self::BEFORE_COMMIT]) && !empty($log_data[self::BEFORE_COMMIT]))
		{
			foreach ($log_data[self::BEFORE_COMMIT] as $i => $log)
			{
				if (!isset($log_data[self::AFTER_COMMIT][$i]))
					continue;
					
				$diff = array_diff_assoc($log_data[self::AFTER_COMMIT][$i], $log);
			
				foreach ($diff as $field => $v)
				{
					if (in_array($field, CMSActiveRecord::$auto_commit_fields) || $field == 'token')
						continue;
			
					$logs[self::BEFORE_COMMIT][$field] = $log[$field];
					$logs[self::AFTER_COMMIT][$field] = $v;
				}
			}
		}
		
		if (isset($log_data[self::ADD_COMMIT]) && !empty($log_data[self::ADD_COMMIT]))
		{
			foreach ($log_data[self::ADD_COMMIT] as $i => $log)
			{
				foreach ($log as $field => $v)
				{
					if (in_array($field, CMSActiveRecord::$auto_commit_fields) || $field == 'token')
						continue;
						
					$logs[self::ADD_COMMIT][$field] = $log[$field];
				}
			}
		}
		
	

		return $logs;
	}
	
	private static function getLogtext($logs)
	{
		$text_before = '';
		$text_after = '';
		$text_insert = '';
		
		foreach ($logs as $ar_name => $log)
		{
			if (isset($log[self::BEFORE_COMMIT]))
			{
				foreach ($log[self::BEFORE_COMMIT] as $k => $v)
					$text_before .= self::$ar_labels[$ar_name][$k] . ':' . $v . ', ';
				foreach ($log[self::AFTER_COMMIT] as $k => $v)
					$text_after .= self::$ar_labels[$ar_name][$k] . ':' . $v . ', ';
			}

			
			if (isset($log[self::ADD_COMMIT]))
			{
				foreach ($log[self::ADD_COMMIT] as $k => $v)
					$text_insert .= self::$ar_labels[$ar_name][$k] . ':' . $v . ', ';
			}
			
		}
		
		$text_before = !empty($text_before) ? 'commit before[' . $text_before . ']' : '';
		$text_after = !empty($text_after) ? 'commit after[' . $text_after . ']' : '';
		
		$text = empty($text_insert) ? '' : 'commit add[' . rtrim($text_insert, ', ') . ']';
		$text .= empty($text_before) ? '' : rtrim($text_before, ', ') . ';' . rtrim($text_after, ', ');
		
		return $text;
	}
}