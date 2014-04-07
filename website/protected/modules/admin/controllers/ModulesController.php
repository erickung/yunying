<?php
/**
 * 
 * @author eric
 *
 */

class ModulesController extends FController 
{
	public function actionList() 
	{
		$modules = ModulesAR::model()->findAllByAttributes(array('parent_module_id'=>0));
		$count = ModulesAR::model()->countByAttributes(array('parent_module_id'=>0));
		
		$this->render('list', array(
				'modules'=>$modules,
				'count'=>$count,
		));
	} 
	
	public function actionModuleInfo()
	{
		if (isset(Request::$get['id']) && Request::$get['id'])
		{
			$module = ModulesAR::model()->findByPk(Request::$get['id']);
			$this->assign('module', $module);
			$cmodules = ModulesAR::model()->findAllByAttributes(array('parent_module_id'=>Request::$get['id']));
			$this->assign('cmodules', $cmodules);
		}
		
		$this->render('module_info');
	}
	
	public function actionCModuleAdd()
	{
		if (!isset(Request::$get['pid']) || !Request::$get['pid'])
			Response::error();
		$this->render('module_child', array('pid'=>Request::$get['pid']));
	}
	
	public function actionEdit()
	{		
		$ModulesAR = new ModulesAR();
		$ModulesAR->setAttributesFromRequest(Request::$post);
		$flag = true;
		if ($ModulesAR->module_id)
		{
			$flag = $ModulesAR->saveCommit('editModule');
			Response::resp($flag, '', '/admin/modules/list');
		}
		else
		{
			$flag = $ModulesAR->saveCommit('addModule');
			Response::resp($flag, '', '/admin/modules/ModuleInfo?id='.$ModulesAR->module_id);
		}
	}
	
	public function actionCEdit()
	{
		if (!isset(Request::$post['parent_module_id']) || !Request::$post['parent_module_id'])
			Response::postFailure();
	
		$ModulesAR = new ModulesAR();
		$ModulesAR->setAttributesFromRequest(Request::$post);
		if (isset(Request::$post['is_action']) && Request::$post['is_action'] == 'on') // action
		{
			$ModulesAR->is_action = 1;
			$flag = $ModulesAR->saveCommit('addModule');
		}
		else 
		{
			$ModulesAR->is_action = 0;
			$flag = $ModulesAR->saveCommit('addModule');
		}
		
		Response::resp($flag, '', '/admin/modules/ModuleInfo?id='.$ModulesAR->parent_module_id);
	}

	 
} 