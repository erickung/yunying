<?php
class SalesTrackController extends FController
{
	function actionList()
	{
		$data = ProductInfoAR::model()->getProductsOrderBySales();
		
		$this->render('list', array(
			'products'=>$data
				
		));
	}
}