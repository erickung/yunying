<?php
class ActiveRecordServ
{
	private static $ins;
	private $adapter;
	private $active_record;
	
	public static function Instance($active_record)
	{
		if (self::$ins !== null)
			return self::$ins;
	
		Yii::import('application.components.service.active_record.*');
		self::$ins = new self();
		self::$ins->setActiveRecordHandle($active_record);
		return self::$ins;
	}
	
	public function getCondition(array $custom_cond, array $reset)
	{
		$searchs = Request::getSearch();
		$this->adapter = $this->getAdapter(array('searchs'=>$searchs, 'custom_cond'=>$custom_cond, 'reset'=>$reset));
		
		return $this->adapter->getCondition();
	}
	
	public function copyAttributesFromAR(ActiveRecordInterface $ar_from, ActiveRecordInterface $ar_to)
	{
		foreach ($ar_from->getAttributes() as $attr => $value)
		{
			if ($this->active_record->primaryKey() == $attr && (is_null($value) || empty($value)))
				continue;
	
			if ($ar_to->isTableFiled($attr) && !is_null($ar_from->{$attr})) $ar_to->setAttribute($attr, $value);
		}
	}
	
	public function copyAttributesFromArray(array $arr_from, ActiveRecordInterface $ar_to)
	{
		$attributes = $ar_to->getAttributes();
		foreach ($attributes as $attr => $v) 
		{
			$value = isset($arr_from[$attr]) ? $arr_from[$attr] : null;
			if ($this->active_record->primaryKey() == $attr && (is_null($value) || empty($value)))
				continue;
			
			$ar_to->setAttribute($attr, $value);
		}
	}
	
	public function setActiveRecordHandle(ActiveRecordInterface $active_record)
	{
		$this->active_record = $active_record;
	}
	
	public function getActiveRecordHandle()
	{
		return $this->active_record;
	}
	
	private function getAdapter(array $params = array())
	{	
		if ($this->active_record instanceof CActiveRecord)
		{
			return new CActiveRecordAdapter($this->active_record, $params);
		}
		elseif ($this->active_record instanceof EMongoEmbeddedDocument)
		{
			return new MongoActiveRecordAdapter($this->active_record, $params);
		}
		else 
		{
			throw new CException("active_record is not the instance of CActiveRecord or EMongoEmbeddedDocument");
		}
	}
}