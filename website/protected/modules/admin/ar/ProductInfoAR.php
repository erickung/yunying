<?php
class ProductInfoAR extends ProductInfo
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function getProductInfoById($id)
	{
		return $this->findByPk($id);
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