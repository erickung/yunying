<?php
class CustomerPurchaseAR extends CustomerPurchase
{
	public $status_name;
	
	public function displays()
	{
		return array(
			'status' => 'getStatusName',
		);
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	function addPurchase()
	{
		$this->user_id = WebUser::Instance()->user->user_id;
		$this->save();
	}
	
	function getProductCustomer($product_id)
	{
		return $this->with('customer')->findAllByAttributes(array('product_id'=>$product_id));
	}
	
	function getStatusName($v)
	{
		static $conf = array();
		if (empty($conf)) $conf = SalesConf::getCustomAppointStatus(); 

		return $conf['show'][$v];
	}
}