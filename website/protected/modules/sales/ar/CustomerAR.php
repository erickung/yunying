<?php
class CustomerAR extends Customer
{	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function displays()
	{
		return array(
				'user_id' => array('UserAR', 'getUserNameByUserid'),
		);
	}
	
	function getCustomByCustomId($custom_id)
	{
		$data = $this->findByPk($custom_id);
		ActiveRecordServ::Instance($data)->resetDisplays();
		
		return $data;
	}
	
	function getCustomByUserId($user_id)
	{
		$data = $this->findAllByAttributes(array('user_id'=>$user_id));
		foreach ($data as $d)
			ActiveRecordServ::Instance($d)->resetDisplays();
		
		return $data;
	}
	
	
}