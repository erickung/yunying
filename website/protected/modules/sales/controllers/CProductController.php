<?php
class CProductController extends FController
{
	protected function beforeAction($action)
	{
		Yii::import('product.ar.*');
		return parent::beforeAction($action);
	}
	
	function actionList()
	{
		$products = ProductInfoAR::model()->findAllByAttributes(array('status'=>99));
		$count = ProductInfoAR::model()->countByAttributes(array('status'=>99));
		
		
		if (isset(Request::$get['type']) && Request::$get['type'] == 'part')
		{
			$this->renderPartial('pro_list_m', array(
					'customer_id' => Request::$get['cid'],
					'products'=>$products,
					'count'=>$count,
			));
		} 
		else 
		{
			$this->render('pro_list_m', array(
					'products'=>$products,
					'count'=>$count,
			));
		}	
	}
	
	function actionAddAppointInfo()
	{
		if (!Request::$get['pid']) return false; 

		$this->render('customer_appoint', array(
			'customer'=> new CustomerAR(),
			'product_id'=>Request::$get['pid'],
		));
	}
	
	function actionAppoint()
	{
		if(!Request::$post['customer_id']) return false;		
		$CustomerPurchaseAR = new CustomerPurchaseAR();
		$CustomerPurchaseAR->setAttributesFromRequest(Request::$post);
		$CustomerPurchaseAR->status = 1;
		$rnt = $CustomerPurchaseAR->saveCommit('addPurchase');
		Response::respThisPage($rnt, 'submitOK()');
	}
	
	function actionCheckSale()
	{
		if(!Request::$post['ps_id']) return false;
		$CustomerPurchaseAR = new CustomerPurchaseAR();
		$CustomerPurchaseAR->setAttributesFromRequest(Request::$post);
		$rnt = $CustomerPurchaseAR->saveCommit('modifyByPk');
		Response::respThisPage($rnt, 'submitOK()');
	}
}