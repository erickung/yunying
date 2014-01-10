<?php
class WebUser extends CWebUser
{
	public $user;
	public $roles;
	public $modules;
	private $all_modules;
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
		$this->all_modules = $this->getAllModuleActions();
		return true;
	}
	
	public function getLoginUer()
	{
		$token = Root::getSession(CMSConsts::CMS_TOKEN);
		if (is_null($token)) return false;
		
		$user = UserAR::model()->find("token='$token'");
		
		if ($user)
			$this->load($user);
		
		return $user;
	}
	
	public function checkPower(CMSController $contr,$params=array())
	{
		$controller_id = ($contr->getModule()) ? $contr->getModule()->getId() . '.' . $contr->getId() : $contr->getId();
		$action_id = $contr->getAction()->getId();
		$module_action = $this->getUserModuleActions();

		foreach ($module_action as $action)
		{
			if ($this->checkMudulePower($action, $controller_id, $action_id))
			{
				$contr->module_id = self::$module_ids[$action];
				return true;
			}
				
		}

		return false;
	}
	
	public function getUserModuleActions()
	{
		$modules = $this->getModuleActions($this->modules);
		return array_flip($modules);
	}
	
	public function getAllModuleActions()
	{
		return $this->getModuleActions(Modules::model()->findAll());	
	}
	
	public function getModuleActions(array $module_actions)
	{
		$modules = array();
		
		if (empty($module_actions)) return $modules;
		$i = 0;
		foreach ($module_actions as $module)
		{
			if (!$module->controller) continue;
			
			$actions = $this->getModuleAction($module);
			foreach ($actions as $k => $m)
			{	
				$modules[strtolower($k)] = $i;
				self::$module_ids[strtolower($k)] = $module->module_id;
				$i++;
			}
		}
		
		return $modules;
	}

	public function getModuleTree($has_ation = false)
	{
		$modules = array();
		foreach ($this->modules as $module)
		{
			if (!$has_ation && $module->isActionModule()) continue;
			
			$module_id = $module->module_id;
			if ($module->isRootModule())
			{
				$modules[$module_id] = array();
			}
			else
			{
				if (isset($modules[$module->parent_module_id]))
					array_push($modules[$module->parent_module_id], $module_id);
				else
					$modules[$module->parent_module_id] = array($module_id);	
			}
		}
		
		return $modules;
	}
	
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
	
	private function load(UserAR $user)
	{
		$this->user = $user;
		$this->roles = $user->getUserRoles();
		$this->modules = $user->getUserModules();
	}
}