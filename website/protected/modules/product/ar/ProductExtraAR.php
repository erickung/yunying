<?php
class ProductExtraAR extends ProductExtra
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function updateExtra($info)
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