<?php

class CustomerController extends Controller
{
	public function actionList()
	{
		$this->render('customer/list');
	}
}