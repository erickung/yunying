<?php
class ProductRoleController extends FController
{
	function actionList()
	{
		$roles = ProductRoleAR::model()->findAll();
		$count = ProductRoleAR::model()->count();
		
		$this->render('list', array(
				'data'=>$roles,
				'count'=>$count,
		));
	}
	
	function actionInfo()
	{
		if (isset(Request::$get['id']) && Request::$get['id']>0)
		{
			$role = ProductRoleAR::model()->findByPk(Request::$get['id']);
			$this->assign('role', $role);
		}
		$this->render('info');
	}
	
	function actionEdit()
	{
		$ProductRoleAR = new ProductRoleAR();
		$ProductRoleAR->setAttributesFromRequest(Request::$post);
		$flag = true;
		if ($ProductRoleAR->pr_id)
		{
			$flag = $ProductRoleAR->saveCommit('updateRole');
		}
		else
		{
			$flag = $ProductRoleAR->saveCommit('addRole');
		}
		
		Response::resp($flag, '', '/admin/ProductRole/list');
	}
}