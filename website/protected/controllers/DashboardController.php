<?php
Yii::import('product.ar.*');
Yii::import('sales.ar.*');
class DashboardController extends FController
{
	function actionIndex()
	{
		
		
		
		$this->render('dashboard');
	}
	
	function actionProductItems()
	{
		$unfinished_products = ProductInfoAR::model()->findAll(array(
				'condition'=>'status<99'
		));
		$unfinished_count = ProductInfoAR::model()->count(array(
				'condition'=>'status<99'
		));
		
		$over_products = ProductInfoAR::model()->findAll(array(
				'condition'=>'status=100'
		));
		$over_count = ProductInfoAR::model()->count(array(
				'condition'=>'status=100'
		));
		
		
		$publish_products = ProductInfoAR::model()->getProductsOrderBySales('real_account',0,10);
		$publish_count = ProductInfoAR::model()->count(array(
				'condition'=>'status=99'
		));
		
		foreach ($unfinished_products as $product)
		{
			$product->status_name = ProductConf::getStatusLabel($product->status);
			$product->check_power = $product->checkPassPower();
		}
	
		
		$DashboardAR = new DashboardAR();
		$this->renderPartial('product_items', array(
				'fields' => array('product_id','name'),
				'unfinished_products'=>$unfinished_products,
				'unfinished_count'=>$unfinished_count,
				'over_products'=>$over_products,
				'over_count'=>$over_count,
				'publish_products'=>$publish_products,
				'publish_count'=>$publish_count,
				'customer'=>new CustomerAR(),
				'is_saler'=>$DashboardAR->isSaler(),
		));
	}
	
	function actionMattersWithDeal()
	{
		Yii::import('service.sales.*');
		$DashboardAR = new DashboardAR();
		if ($DashboardAR->isSaler() && !$DashboardAR->isProductManager())
		{
			list($products, $count) = $DashboardAR->CustomerDealMatters();
			if (isset(Request::$post['notify']))
			{
				echo $count;exit();
			} 

			$this->renderPartial('CustomerDealMatters', array(
				'products'=>$products,
				'count'=>$count,
			));
		}
		else 
		{
			list($products, $count) =  $DashboardAR->productDealProcess();

			if ($DashboardAR->isProductManager()) 
			{
				if (isset(Request::$post['notify']))
				{
					echo $count;exit();
				}
				if (!empty($products))
				{
					$this->renderPartial('ProductDealProcess', array(
							'products'=>$products,
							'count'=>$count,
					));
				}
			}
			else 
			{
				list($products, $count) = $DashboardAR->productDealMatters();
				if (isset(Request::$post['notify']))
				{
					echo $count;exit();
				}
				$this->renderPartial('ProductDealMatters', array(
						'products'=>$products,
						'count'=>$count,
						'status'=>SalesConf::getCustomAppointStatus(),
				));
			}

			
			
		} 

	}
	
}