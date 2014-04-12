<?php
class ManageController extends FController
{
	function actionList()
	{
		$products = ProductInfoAR::model()->findAll();
		$count = ProductInfoAR::model()->count();
		
		$this->render('list', array(
				'fields' => array('product_id','name'),
				'products'=>$products,
				'count'=>$count,
		));
		$this->render('list');
	}
	
	function actionInfo()
	{
		if (isset(Request::$get['id']) && Request::$get['id']>0)
		{
			$product = ProductInfoAR::model()->getProductInfoById(Request::$get['id']);
			$this->assign('product', $product);
		}
		
		$this->render('info');
	}
	
	function actionEdit()
	{
		$ProductInfoAR = new ProductInfoAR();
		$ProductInfoAR->setAttributesFromRequest(Request::$post);

		$flag = true;
		if ($ProductInfoAR->product_id)
		{
			$flag = $ProductInfoAR->saveCommit('updateProduct');
		}
		else
		{
			$flag = $ProductInfoAR->saveCommit('addProduct');
		}
		
		Response::resp($flag, '', '/product/manage/list');
	}
}