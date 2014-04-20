<?php

class CustomerController extends FController
{
	public function actionList()
	{
		$customers = CustomerAR::model()->findAllByAttributes(array(
			'user_id'=>WebUser::Instance()->user->user_id
		));
		$count = CustomerAR::model()->count();
		
		foreach ($customers as $customer)
			ActiveRecordServ::Instance($customer)->resetDisplays();
		
		$this->render('list', array(
				'fields' => array('product_id','name'),
				'customers'=>$customers,
				'count'=>$count,
		));
	}
	
	public function actionInfo()
	{
		
		if (isset(Request::$get['id']) && Request::$get['id']>0)
		{
			$customer = CustomerAR::model()->getCustomByCustomId(Request::$get['id']);
		}
		else
		{
			$customer = new CustomerAR();
		}
		
		$this->assign('customer',$customer);
		$this->render('info');
	}
	
	function actionEdit()
	{
		$customer = new CustomerAR();

		if (Request::$post['customer_id']) 
		{
			$flag = $customer->saveCommit('updateCustomer', Request::$post);
		}
		else 
		{
			$flag = $customer->saveCommit('addCustomer', Request::$post);
		}
		
		Response::resp($flag, '', '/sales/customer/list');
	}
	

	function actionCustomerPurchase()
	{
		Yii::import('service.sales.*');
		if (!Request::$get['cid']) return false;

		$products = CustomerPurchaseAR::model()
			->findAllByAttributes(array('customer_id'=>Request::$get['cid']));
		
		$count = CustomerPurchaseAR::model()->countByAttributes(array('customer_id'=>Request::$get['cid']));
		
		foreach ($products as $product)
			$product->status_name = $product->getStatusName($product->status);
		
		$this->renderPartial('purchase_info', array(
				'cid'=>Request::$get['cid'],
				'products'=>$products,
				'count'=>$count,
				'status'=>SalesConf::getCustomAppointStatus(),
		));
	}
}