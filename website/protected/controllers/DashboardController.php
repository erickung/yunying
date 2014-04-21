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
			$product->status_name = ProductConf::getStatusLabel($product->status);
		
		$this->renderPartial('product_items', array(
				'fields' => array('product_id','name'),
				'unfinished_products'=>$unfinished_products,
				'unfinished_count'=>$unfinished_count,
				'over_products'=>$over_products,
				'over_count'=>$over_count,
				'publish_products'=>$publish_products,
				'publish_count'=>$publish_count,
				'customer'=>new CustomerAR(),
		));
	}
}