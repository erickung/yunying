<?php
class ProductController extends Controller
{
	function actionList()
	{
		$this->render('product/list');
	}
}