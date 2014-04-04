<?php

class CustomerController extends FController
{
	public function actionList()
	{
		$this->render('customer/list');
	}
}