<?php
class CustomerPurchaseAR extends CustomerPurchase
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	function addPurchase()
	{
		$this->save();
	}
}