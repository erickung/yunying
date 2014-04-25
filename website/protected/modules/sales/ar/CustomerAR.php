<?php
class CustomerAR extends Customer
{	
	public $sex_data;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function __construct($s='insert')
	{
		parent::__construct($s);
		$this->initConf();
	}
	
	function initConf()
	{
		$this->sex_data = CommonConf::getSex();
	}
	
	public function displays()
	{
		return array(
				'user_id' => array('UserAR', 'getUserNameByUserid'),
		);
	}
	
	function addCustomer($info)
	{
		$this->setAttributesFromRequest($info);
		$this->user_id = WebUser::Instance()->user->user_id;
		$this->save();
		if (isset($info['appoint_account'])) 
		{
			$CustomerPurchaseAR = new CustomerPurchaseAR();
			$CustomerPurchaseAR->customer_id = $this->customer_id;
			$CustomerPurchaseAR->product_id = $info['product_id'];
			$CustomerPurchaseAR->appoint_account = $info['appoint_account'];
			$CustomerPurchaseAR->user_id = WebUser::Instance()->user->user_id;
			$CustomerPurchaseAR->status = 1;
			$CustomerPurchaseAR->save();
		}

	}
	
	function updateCustomer($info)
	{
		$this->setAttributesFromRequest($info);
		$this->user_id = WebUser::Instance()->user->user_id;
		$this->modifyByPk($this->customer_id);
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
	
	function getUserCustomers($user_id)
	{
		if ($user_id) 	
			$data = $this->findAllByAttributes(array('user_id'=>$user_id));
		else 
			$data = $this->findAll();
		$rnt = array();
		foreach ($data as $d)
		{
			$rnt[$d->customer_id] = $d->name;
		}
		return $rnt;
	}
	
	
}