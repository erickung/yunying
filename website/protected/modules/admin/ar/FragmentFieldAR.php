<?php
class FragmentFieldAR extends FragmentField
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function getBlockFields($db_id) {
		$fragmentFields = self::model()->findAll(array(
				'condition' => 'db_id = :db_id',
				'params' => array(':db_id'=>$db_id),
				'order' => 'dbc_ordering asc, dbc_id asc',
		));
		$data = CMS::convetARToList($fragmentFields, array_keys(FragmentFieldAR::model()->attributeLabels()));
		foreach($data as &$val) {
			$val['dbc_data'] = @unserialize($val['dbc_data']);
			$val['dbc_args'] = @unserialize($val['dbc_args']);
		}
		unset($val);
		return $data;
	}
	
	public function getSelectFields(array $condition = array())
	{
		/*
		$where = array();
		$conditions = array_merge($where, $condition);
		
		$con = '';
		foreach ($conditions as $k => $v)
			$con[] = "$k='$v'";
		$condition = implode(' and ', $con);
		
		return self::model()->findAll($condition);
		*/
		return $this->findAllByAttributes($condition);
	}
	
	public function getFieldCopyByFieldid(array $fieldids)
	{
		$fieldids = implode(',', $fieldids);
		$data = $this->findAll("dbc_id in ($fieldids)");
		
		$rnt = array();
		foreach ($data as $d)
		{
			$rnt[] = $d->attributes;
		}
		
		return $rnt;
	}
	
	public function getFieldCopy()
	{
		$data = $this->findAllByAttributes(array('db_id' => $this->db_id));

		$rnt = array();
		foreach ($data as $d)
		{
			$rnt[$d->attributes['dbc_id']] = $d->attributes;
		}
		ksort($rnt);
		
		return $rnt;
	}
	
	protected function copyToField(array $copy)
	{
		if (empty($copy)) return false;
		
		foreach ($copy as $arr)
		{
			$ff = new FragmentFieldAR();
			$ff->copyAttributesFromArray($arr, $ff);
			$ff->{$ff->getMetaData()->tableSchema->primaryKey} = null;
			$ff->db_id = $this->db_id;
			$ff->save();
		}
	}
	
	public static function getFragmentFieldByUniqueKey($db_id,$dbc_name)
	{
		$ar = self::model()->findByAttributes(array('db_id'=>$db_id, 'dbc_name'=>$dbc_name));
		return ($ar instanceof CActiveRecord) ? CMS::convetARToArray($ar) : null;
	}
	
	protected function addFragmentField()
	{
		$this->save();
	}
	
	protected function updateFragmentField()
	{
		$this->modifyByPk();
	}
	
	protected function deleteFragmentField()
	{
		self::model()->deleteByPk($this->getPrimaryKey());
	}
	
	protected function deleteBydb_id()
	{
		$this->deleteAllByAttributes(array('db_id'=>$this->db_id));
	}
	
}