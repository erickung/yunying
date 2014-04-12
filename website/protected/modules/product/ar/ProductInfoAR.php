<?php
class ProductInfoAR extends ProductInfo
{
	public function saveRule()
	{
		return array(
				'build_date'=>array('strtotime',1),
				'publish_start_date'=>array('strtotime',1),
				'publish_end_date'=>array('strtotime',1),
		);
	}
	
	public function displays()
	{
		return array(
				'build_date' => ActiveRecordInterface::TIME_TO_DATE,
				'publish_start_date' => ActiveRecordInterface::TIME_TO_DATE,
				'publish_end_date' => ActiveRecordInterface::TIME_TO_DATE,
		);
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function getProductInfoById($id)
	{
		$product = $this->findByPk($id);
		ActiveRecordServ::Instance($product)->resetDisplays();
		return $product;
	}
	
	protected function updateProduct()
	{
		$this->modifyByPk();
	}
	
	protected function addProduct()
	{
		$this->save();
	}
}