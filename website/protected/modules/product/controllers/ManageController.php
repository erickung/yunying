<?php
class ManageController extends FController
{
	function actionList()
	{
		$products = ProductInfoAR::model()->findAll();
		$count = ProductInfoAR::model()->count();
		
		foreach ($products as $product)
		{
			$product->status_name = ProductConf::getStatusLabel($product->status);
			$product->check_power = $product->checkPassPower();
		}

		$this->render('list', array(
				'fields' => array('product_id','name'),
				'products'=>$products,
				'count'=>$count,
				'product_roles'=>ProductRoleAR::model()->getProductRoles(),
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
			if (!$publish)  $publish = new ProductPublishAR();
			

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
		$this->assign('productRoles', $product->process->productRoles);
		
		if (isset(Request::$get['ref'])) $this->assign('ref', Request::$get['ref']);

		$this->render('static');
	}
	
	function actionCustomerStatic()
	{
		if (!isset(Request::$get['id']) || !Request::$get['id'])
			Response::errorPage();
		
		$product = ProductInfoAR::model()->getProductInfoById(Request::$get['id']);
		$product_roles = $product->getProductProcessRoles();
		$this->assign('product_roles', $product_roles);
		$this->assign('product', $product);
		
		$publish = ProductPublishAR::model()->getPublishInfoById(Request::$get['id']);
		$this->assign('publish', !$publish ? new ProductPublishAR() : $publish);
		
		$extra_info = is_null($product->productExtra) ? new ProductExtraAR() : $product->productExtra;
		$this->assign('extra_info', $extra_info);
		
		$approval_items = ProductApprovalItemAR::model()->getItemsByProductId(Request::$get['id']);
		$this->assign('approval_items', $approval_items);
		
		$this->render('customer_static');
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
			/*
			if ($ProductInfoAR->saveCommit('addProduct', Request::$post)) 
				$flag = $ProductInfoAR->saveCommit('addOtherInfo', Request::$post);
			*/
			$flag = $ProductInfoAR->saveCommit('addProduct', Request::$post);
		}
		
		$url = '';
		if (Request::$post['status'] == 1)
			$url = '/product/manage/list';
		else 
			$url = '/product/manage/info?id='.$ProductInfoAR->product_id;

		
		Response::resp($flag, '', $url);
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
		
		if (isset(Request::$post['ref']) && Request::$post['ref']) 
			Response::resp($flag, '', Request::$post['ref']);
		else 
			Response::resp($flag, '', '/product/manage/list');

		
	}
	
	function actionProductFiles()
	{
		if (!isset(Request::$post['id']) || !Request::$post['id'])
			exit(); 

		$product_files = ProductFilesAR::model()->findAllByAttributes(array('product_id'=>Request::$post['id']));
		$this->assign('files', $product_files);
		if (isset(Request::$post['static']))
			$this->assign('static', 1);

		echo $this->fetch('product.manage.uploadedfiles');
	}
	
	function actionDeleteFile()
	{
		ProductFilesAR::model()->deleteByPk($_POST['file_id']);
		Response::showPostMsg("eric.product_files.deleteSuccess();");
	}
	
	function actionDownFile()
	{
		if (!isset(Request::$get['id']) || !Request::$get['id'])
			exit();
		
		$ProductFilesAR = new ProductFilesAR();
		$ProductFilesAR->file_id = Request::$get['id'];
		$ProductFilesAR->downFile();
	}
	
	function actionProductDetail()
	{
		if (!isset(Request::$get['id']) || !Request::$get['id'])
			exit();
	
		Yii::import('sales.ar.*');
		Yii::import('service.sales.*');
		if (isset(Request::$get['type']) && Request::$get['type'] == 'part')
		{	
			if (Request::$get['status'] == 'all') Request::$get['status'] = null; 

			$CustomerPurchaseAR = new CustomerPurchaseAR();
			$data = $CustomerPurchaseAR->getProductCustomer(Request::$get['id'], Request::$get['status']);
			foreach ($data as $d)
				$d->status_name = $d->getStatusName($d->status);
			
				
			$this->renderPartial('load_customer_detail', array(
					'data'=>$data,
					'status'=>SalesConf::getCustomAppointStatus(),
			));
			
			
		} 
		else 
		{
			$this->render('customer_detail', array(
					'pid'=>Request::$get['id'],
					'status'=>SalesConf::getCustomAppointStatus(),
			));
		}
		

	}
	
	function actionCheckSale()
	{
		Yii::import('sales.ar.*');
		if(!Request::$post['ps_id']) return false;
		$CustomerPurchaseAR = new CustomerPurchaseAR();
		$CustomerPurchaseAR->setAttributesFromRequest(Request::$post);
		$rnt = $CustomerPurchaseAR->saveCommit('modifyByPk');
		Response::respThisPage($rnt, 'submitOK()');
	}
}