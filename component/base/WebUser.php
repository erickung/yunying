<?php
class WebUser extends CWebUser
{
	public $user;
	public $roles;
	public $modules;
	private $all_modules;
	private $all_module_tree;
	private $first_module;
	private static $module_ids;
	
	/**
	 * 
	 * @return WebUser
	 */
	public static function Instance()
	{
		Yii::import('application.modules.admin.ar.*');
		if (Yii::app()->user)
			return Yii::app()->user;
	
		return Yii::app()->user = new self();
	}
	
	public function auth()
	{
		$user = $this->getLoginUer();
		if (!$user) return false;	
		
		$this->load($user);
		$this->getModuleActions();
		return true;
	}
	
	public function getLoginUer()
	{
		$token = Root::getCookie(RootConsts::TOKEN);
		if (is_null($token)) return false;
		
		$user = UserAR::model()->find("token='$token'");
		
		if ($user)
			$this->load($user);
		
		return $user;
	}
	
	public function checkPower(RootController $contr,$params=array())
	{
		$controller_id = ($contr->getModule()) ? $contr->getModule()->getId() . '.' . $contr->getId() : $contr->getId();
		$action_id = $contr->getAction()->getId();

		if (isset($contr::$defaultModules[$controller_id]) && in_array($action_id, $contr::$defaultModules[$controller_id]))
		{
			$module = new stdClass();
			$module->href = str_replace('.', '/', $controller_id) . '/' . $action_id;
			$contr->assignModules($this->all_modules, $this->all_module_tree, $module);
			return true;
		}
				
		foreach ($this->all_modules as $module)
		{
			if ($module->controller == $controller_id && $module->action == $action_id)
			{
				$contr->assignModules($this->all_modules, $this->all_module_tree, $module);
				return true;
			}	
		}
		return false;
	}
	
	public function getModuleActions()
	{
		foreach ($this->modules as $module)
		{
			if ($module->is_action) continue;
			
			if ($module->parent_module_id == 0)
			{
				if (!isset($this->all_module_tree[$module->module_id])) $this->all_module_tree[$module->module_id] = array();
			}
			else
			{
				if (!isset($this->all_module_tree[$module->parent_module_id]))
					$this->all_module_tree[$module->parent_module_id] = array();

				array_push($this->all_module_tree[$module->parent_module_id], $module->module_id);
			}	
			
			$module->href = str_replace('.', '/', $module->controller) . '/' . $module->action;
			$this->all_modules[$module->module_id] = $module;
		}
	}
	
	public function getFirstModule()
	{
		foreach ($this->all_module_tree as $modules)
		{
			foreach ($modules as $m)
			{
				return $this->all_modules[$m]->href;
			}
		}
	}
	
	
	/*
	private function getModuleAction(Modules $module)
	{
		$rnt = array();
		$actions = explode(',', $module->action);
		foreach ($actions as $action)
			$rnt[$module->controller . '.' . $action] = $module;
		
		return $rnt;
	}
	
	private function checkMudulePower($action, $controller_id, $action_id)
	{
		$req_action = strtolower($controller_id) . '.' . strtolower($action_id);
		if (!isset($this->all_modules[$req_action]))
			return true;
		
		return ($action == $req_action);
	}
	*/
	
	private function load(UserAR $user)
	{
		$this->user = $user;
		$this->roles = $user->getUserRoles();
		$this->modules = $user->getUserModules();	
	}
}