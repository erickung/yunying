<?php
class ManageController extends FController
{
	function actionList()
	{
		$products = ProductInfoAR::model()->findAll();
		$count = ProductInfoAR::model()->count();
		
		foreach ($products as $product)
			$product->status_name = ProductConf::getStatusLabel($product->status);

		$this->render('list', array(
				'fields' => array('product_id','name'),
				'products'=>$products,
				'count'=>$count,
		));
	}
	
	function actionInfo()
	{
		if (isset(Request::$get['id']) && Request::$get['id']>0)
		{
			$product = ProductInfoAR::model()->getProductInfoById(Request::$get['id']);
			
			$product_roles = $product->getProductProcessRoles();
			$this->assign('product_roles', $product_roles);
			
			// 是否有通过权限
			$power = $product->checkPassPower();
			$this->assign('power', $power);
			
			$publish = ProductPublishAR::model()->getPublishInfoById(Request::$get['id']);
		}
		else 
		{
			$product = new ProductInfoAR();
			$publish = new ProductPublishAR();
		}

		$this->assign('product', $product);
		$this->assign('publish', $publish);
		
		$extra_info = is_null($product->productExtra) ? new ProductExtraAR() : $product->productExtra;
		$this->assign('extra_info', $extra_info);

		$this->render('info');
	}
	
	function actionStatic()
	{
		if (!isset(Request::$get['id']) || !Request::$get['id'])
			Response::errorPage(); 

		$product = ProductInfoAR::model()->getProductInfoById(Request::$get['id']);
		$product_roles = $product->getProductProcessRoles();
		$this->assign('product_roles', $product_roles);
		// 是否有通过权限
		$power = $product->checkPassPower();
		$this->assign('power', $power);
		$this->assign('product', $product);
		
		$publish = ProductPublishAR::model()->getPublishInfoById(Request::$get['id']);
		$this->assign('publish', !$publish ? new ProductPublishAR() : $publish);
		
		$extra_info = is_null($product->productExtra) ? new ProductExtraAR() : $product->productExtra;
		$this->assign('extra_info', $extra_info);
		
		$approval_items = ProductApprovalItemAR::model()->getItemsByProductId(Request::$get['id']);
		$this->assign('approval_items', $approval_items);
		
		$this->render('static');
	}
	
	
	function actionEdit()
	{
		$ProductInfoAR = new ProductInfoAR();

		$flag = true;
		if (Request::$post['product_id'])
		{
			$flag = $ProductInfoAR->saveCommit('updateProduct', Request::$post);	
		}
		else
		{
			$flag = $ProductInfoAR->saveCommit('addProduct',Request::$post);
		}
		
		Response::resp($flag, '', '/product/manage/list');
	}
	
	
	
	function actionPause()
	{
		
	}
	
	function actionApproval()
	{
		if (Request::$post['pass_type'] == '1')
		{
			$ProductApproval = new ProductApproval();
			$flag = $ProductApproval->saveCommit('passProduct', Request::$post);
		}
		elseif (Request::$post['pass_type'] == '-1')
		{
			$ProductApproval = new ProductApproval();
			$flag = $ProductApproval->saveCommit('rejectProduct', Request::$post);
		}
		
		Response::resp($flag, '', '/product/manage/list');
	}
}