<?php
class DashboardAR
{
	function productDealMatters()
	{
		$criteria = new CDbCriteria();
		$criteria->addInCondition('status', array(1,3));
		$products = CustomerPurchaseAR::model()->findAll($criteria);
		foreach ($products as $product)
		{
			$product->status_name = $product->getStatusName($product->status);
			$product->customer_id = $product->getCustomerName($product->customer_id);
			$product->product_id = $product->getProductName($product->product_id);
		}
	
		$count = CustomerPurchaseAR::model()->count($criteria);
		
		return array($products, $count);
	}
	
	function CustomerDealMatters()
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition("user_id = " . WebUser::Instance()->user->user_id);
		$criteria->addInCondition('status', array(0,2,4));

		$products = CustomerPurchaseAR::model()->findAll($criteria);
		foreach ($products as $product)
		{
			$product->status_name = $product->getStatusName($product->status);
			$product->customer_id = $product->getCustomerName($product->customer_id);
			$product->product_id = $product->getProductName($product->product_id);
		}
		
		$count = CustomerPurchaseAR::model()->count($criteria);
		
		return array($products, $count);
	}
	
	function isSaler()
	{
		return isset(WebUser::Instance()->roles[3]);
	}
}