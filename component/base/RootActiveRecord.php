<?php
class RootActiveRecord extends CActiveRecord implements ActiveRecordInterface, ActiveRecordAppInterface
{
	const MODIFY_TIME_FIELD = 'modify_time';
	const MODIFY_USER_FIELD = 'modify_username';
	const DB_CORSAIR = 'corsair';
	const DB_POCKET = 'db';
	public static $auto_commit_fields = array(
			self::MODIFY_TIME_FIELD, 
			self::MODIFY_USER_FIELD,
	);
	protected $database = self::DB_POCKET;
	private $databases = array(
			self::DB_CORSAIR => 	self::DB_CORSAIR,
			self::DB_POCKET => 	self::DB_POCKET,
	);
	private $_transaction;		

	public function behaviors()
	{
		return array(
				'application.components.behaviors.UpdateBehavior',
		);
	}
	
	public function attributeLabels()
	{
		return array(				
				'modify_time' => '最后修改时间',
				'modify_user_id' => '最后修改人'
		);
	}
	
	public function getDbConnection()
	{
		if (isset(self::$db[$this->database]) && self::$db[$this->database] !== null)
			return self::$db[$this->database];
		else
		{
			self::$db[$this->database] = Yii::app()->{$this->database};
			if(self::$db[$this->database] instanceof CDbConnection)
				return self::$db[$this->database];
			else
				throw new CDbException(Yii::t('yii','Active Record requires a "db" CDbConnection application component.'));
		}
	}
	
	public function saveCommit($action)
	{
		if (!method_exists($this, $action))
			throw new CException("the " . get_class($this) . "has no method {$action}");
		
		$args = func_get_args();
		array_shift($args);
		
		$this->beginTransaction();
		//$is_new = $this->getIsNewRecord();
		try
		{		
			call_user_func_array(array($this, $action), $args);
			$this->commit();
		}
		catch (CDbException $e)
		{var_dump($e->getMessage());
			$this->rollBack();
			return false;
		}
		
		//OperateLogServ::Instance()->addLogAfterCommit($this, $is_new);
		return true;
	}

	public function add()
	{
		try
		{
			return $this->save();
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	
	public function getCondition(array $custom_cond = array(), array $reset_key = array())
	{
		return ActiveRecordServ::Instance($this)->getCondition($custom_cond, $reset_key);
	}
	
	public function primaryKey()
	{
		return $this->getMetaData()->tableSchema->primaryKey;
	}
	
	public function copyAttributesFromAR($ar_from, $ar_to)
	{
		ActiveRecordServ::Instance($this)->copyAttributesFromAR($ar_from, $ar_to);
	}
	
	public function copyAttributesFromArray($arr_from, $ar_to)
	{
		ActiveRecordServ::Instance($this)->copyAttributesFromArray($arr_from, $ar_to);
	}
	
	protected function modifyByPk()
	{
		try
		{
			$ar = $this->findByPk($this->getPrimaryKey());
			if (is_null($ar) || !$ar instanceof CActiveRecord)
				return false;
				
			OperateLogServ::addLogBeforeCommit($ar);
			$this->copyAttributesFromAR($this, $ar);
			$ar->save();
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	
	public function setAttributesFromRequest(array $res)
	{
		$this->copyAttributesFromArray($res, $this);
	}
	
	public function isTableFiled($name)
	{
		return isset($this->getMetaData()->columns[$name]);
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function beginTransaction()
	{
		$this->_transaction = $this->dbConnection->beginTransaction();
	}
	
	protected function commit()
	{
		$this->_transaction->commit();
	}

	protected function rollback()
	{
		$this->_transaction->rollback();
	}
}