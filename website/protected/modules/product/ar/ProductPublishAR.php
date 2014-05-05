<?php
class ProductPublishAR extends ProductPublish
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function saveRule()
	{
		return array(
				'start_date'=>array('strtotime',1),
				'end_date'=>array('strtotime',1),
		);
	}
	
	public function displays()
	{
		return array(
				'start_date' => array('FTool', 'date'),
				'end_date' => array('FTool', 'date'),
		);
	}

	public function getPublishInfoById($id)
	{
		$info = $this->findByPk($id);
		if ($info) ActiveRecordServ::Instance($info)->resetDisplays();
		return $info;
	}
	
	public function updatePublish($info)
	{
		$this->setAttributesFromRequest($info);
		$ar = $this->findByPk($this->product_id);

		if ($ar)
			$this->modifyByPk();
		else 
		{
			$this->setIsNewRecord(true);
			$this->save();
		}
	}
}
