<?php
class ProductApprovalItemAR extends ProductApprovalItem
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function displays()
	{
		return array(
				'user_id' => array('UserAR', 'getUserNameByUserid'),
				'type' => 'getTypeName',
		);
	}

	public function save($runValidation=true,$attributes=null)
	{
		$this->user_id = WebUser::Instance()->user->user_id;
		return parent::save();
	}
	
	public function getItemsByProductId($id)
	{
		$items = $this->findAllByAttributes(array('product_id'=>$id));
		
		foreach ($items as $item)
			ActiveRecordServ::Instance($item)->resetDisplays();
		
		return $items;
	}
	
	public function getTypeName($type)
	{
		return $type == 1 ? '通过' : '驳回';
	}
}