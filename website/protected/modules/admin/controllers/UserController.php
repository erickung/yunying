<?php
/**
 * 
 * @author eric
 *
 */

class UserController extends FController
{ 
	public function actionList()
	{
		$users = UserAR::model()->findAll();
		$count = UserAR::model()->count();		
		
		$this->render('list', array(
			'users'=>$users,
			'count'=>$count,
		));		
	}
	
	public function actionUserStatic()
	{
		if (!isset(Request::$get['user_id']) || !Request::$get['user_id'])
			return false;

		$user = UserAR::model()->findByPk(Request::$get['user_id']);
		$this->render('user_static', array('user'=>$user));
	}
	
	public function actionUserinfo()
	{
		if (isset(Request::$get['user_id']) && Request::$get['user_id']>0)
		{
			$user = UserAR::model()->findByPk(Request::$get['user_id']);
			$this->assign('user', $user);
		}
		else 
		{
			$user = new UserAR();

		}
		$user_roles = $user->getAllUserRoles();
		$user_products = $user->getAllProductRoles();
		
		$this->assign('user_roles', $user_roles);
		$this->assign('user_products', $user_products);
		$this->render('userinfo');
	}
	
	function actionUserEdit()
	{
		$UserAR = new UserAR();

		$UserAR->setAttributesFromRequest(Request::$post);
		$roles = Request::$post['role_name'];
		$product_roles = Request::$post['pr_name'];

		$flag = true;
		if ($UserAR->user_id)
		{
			$flag = $UserAR->saveCommit('updateUser',$roles, $product_roles);
		}
		else
		{
			$flag = $UserAR->saveCommit('addUser',$roles,$product_roles);
		}
		
		Response::resp($flag, '', '/admin/user/list');
	}
}