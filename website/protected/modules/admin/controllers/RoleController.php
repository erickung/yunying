<?php
/**
 * 
 * @author eric
 *
 */

class RoleController extends FController
{
	public function actionList()
	{
		$roles = RoleAR::model()->findAll();
		$count = RoleAR::model()->count();		
		
		$this->render('list', array(
			'data'=>$roles,
			'count'=>$count,
		));		
	}
	
	public function actionRoleinfo()
	{	
		if (isset(Request::$get['role_id']) && Request::$get['role_id']>0)
		{
			$role = RoleAR::model()->findByPk(Request::$get['role_id']);
			$this->assign('role', $role);
			$role_modules = $role->getAllRoleModules();
		}
		else 
		{
			$role_modules = RoleAR::model()->getAllRoleModules();
		}
		
		$this->assign('role_modules', $role_modules);
		$this->render('role_info');
	}
	
	public function actionEdit()
	{
		$RoleAR = new RoleAR();
		$RoleAR->setAttributesFromRequest(Request::$post);
		$modules = Request::$post['module_name'];
		$flag = true;
		if ($RoleAR->role_id)
		{
			$flag = $RoleAR->saveCommit('updateRole',$modules);
		}
		else
		{
			$flag = $RoleAR->saveCommit('addRole',$modules);
		}
		
		Response::resp($flag, '', '/admin/role/list');
	}
	
	
}