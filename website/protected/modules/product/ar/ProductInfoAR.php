<?php
Yii::import('application.components.service.product.*');

class ProductInfoAR extends ProductInfo
{
	const STATUS_PASS = 99;
	const STATUS_DRAFT = 0;
	
	public $pass_type = -1;	//-1驳回，1通过
	public $trust_type = array();
	public $investment_ways = array();
	public $sales_account = 0;	//销售金额
	public $status_name;
	public $type_name;
	public $investment_way_name;
	
	private static $static_conf = array();
	
	public function __construct($s='insert')
	{
		parent::__construct($s);
		$this->initConf();
	}
	
	public function saveRule()
	{
		return array(
				'build_date'=>array('strtotime',1),
		);
	}
	
	public function displays()
	{
		return array(
				'build_date' => ActiveRecordInterface::TIME_TO_DATE,
		);
	}
	
	function getProductProcessRoles()
	{
		$product_roles = ProductRole::model()->with(array(
				'processes' => array('condition'=>"processes.process_id={$this->process_id}")
		))->findAll();
		
		$rnt = array();
		foreach ($product_roles as $p)
		{
			$rnt[$p->pr_id] = $p->pr_name;	
		}
		return $rnt;
	}
	
	public function checkPassPower()
	{
		if ($this->status == 0) return 1; 

		$power = 0;
		if (!empty(WebUser::Instance()->user->productRoles))
		{
			foreach (WebUser::Instance()->user->productRoles as $p)
			{
				if ($p->pr_id == $this->status) $power = 1;
			}
		}
		return $power;
	}
	
	public function getProductInfoById($id)
	{
		$product = $this->findByPk($id);

		ActiveRecordServ::Instance($product)->resetDisplays();
		$product->type_name = $this->trust_type[$product->type];
		$product->investment_way_name = $this->investment_ways[$product->investment_way];
		
		return $product;
	}

	protected function updateProduct($info)
	{
		$this->setAttributesFromRequest($info);
		$ar = ProductInfoAR::model()->findByPk($this->product_id);
		if (!$ar->checkPassPower()) return false; 
		
		$this->modifyByPk();
		ProductPublishAR::model()->updatePublish($info);
		ProductExtraAR::model()->updateExtra($info);
	}
	
	protected function addProduct($info)
	{	
		$this->setAttributesFromRequest($info);
		$this->save();
		
		$ProductPublishAR = new ProductPublishAR();
		$ProductPublishAR->product_id = $this->product_id;
		$ProductPublishAR->updatePublish($info);
		
		$ProductExtraAR = new ProductExtraAR();
		$ProductExtraAR->product_id = $this->product_id;
		$ProductExtraAR->updateExtra($info);
	}
	
	protected function addOtherInfo($info)
	{

	}
	
	protected function initConf()
	{
		$this->trust_type = isset(self::$static_conf['trust_type']) ? 
			self::$static_conf['trust_type'] : ProductConf::getTrustType();
		$this->investment_ways = isset(self::$static_conf['investment_ways']) ?
			self::$static_conf['investment_ways'] : ProductConf::getInvestmentWay();
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}